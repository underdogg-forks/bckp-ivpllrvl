<?php

namespace Modules\Quotes\Controllers;

use AllowDynamicProperties;
use Illuminate\Support\Facades\Log;
use Modules\Clients\Services\ClientsService;
use Modules\Core\Controllers\AdminController;
use Modules\CustomFields\Services\QuoteCustomService;
use Modules\InvoiceGroups\Services\InvoiceGroupsService;
use Modules\Quotes\Services\QuoteAmountsService;
use Modules\Quotes\Services\QuoteItemsService;
use Modules\Quotes\Services\QuotesService;
use Modules\Quotes\Services\QuoteTaxRatesService;
use Modules\TaxRates\Services\TaxRatesService;
use Modules\Units\Services\UnitsService;
use Modules\Users\Services\UsersService;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    public $ajax_controller = true;

    /**
     * Validate and persist a quote with its items, discounts, and custom fields, then emit a JSON success or validation error response.
     *
     * Performs request validation, saves or updates the quote record and its items (including applying a global discount and honoring legacy calculation mode),
     * generates a quote number when required, recalculates amounts if legacy calculation is enabled, persists custom field values, and exits after sending a JSON response
     * that indicates success or contains validation errors.
     */
    public function save()
    {
        $quote_id = $this->security->xss_clean($this->input->post('quote_id', true));
        (new QuotesService())->setId($quote_id);
        if ((new QuotesService())->runValidation('validation_rules_save_quote')) {
            $items                  = json_decode($this->input->post('items'));
            $quote_discount_percent = (float) $this->input->post('quote_discount_percent');
            $quote_discount_amount  = (float) $this->input->post('quote_discount_amount');
            // Percent by default. Only one allowed. Prevent set 2 global discounts by geeky client - since v1.6.3
            if ($quote_discount_percent && $quote_discount_amount) {
                $quote_discount_amount = 0.0;
            }
            // New discounts (for legacy_calculation false) - since v1.6.3 Need if taxes applied after discounts
            $items_subtotal = 0.0;
            if ($quote_discount_amount) {
                foreach ($items as $item) {
                    if ( ! empty($item->item_name)) {
                        $items_subtotal += standardize_amount($item->item_quantity) * standardize_amount($item->item_price);
                    }
                }
            }
            // New discounts (for legacy_calculation false) - since v1.6.3 Need if taxes applied after discounts
            $global_discount = [
                'amount'  => $quote_discount_amount ? standardize_amount($quote_discount_amount) : 0.0,
                'percent' => $quote_discount_percent ? standardize_amount($quote_discount_percent) : 0.0,
                'item'    => 0.0,
                // Updated by ref (Need for quote_item_subtotal calculation in Mdl_quote_amounts)
                'items_subtotal' => $items_subtotal,
            ];
            // Automatic calculation mode
            if (get_setting('einvoicing')) {
                // Shift to false (by default). Need true? See Dev Note on ipconfig example
                $this->config->set_item('legacy_calculation', ! empty($this->input->post('legacy_calculation')));
            }
            foreach ($items as $item) {
                // Check if an item has either a quantity + price or name or description
                if ( ! empty($item->item_name)) {
                    // Standardize item data
                    $item->item_quantity        = $item->item_quantity ? standardize_amount($item->item_quantity) : 0.0;
                    $item->item_price           = $item->item_price ? standardize_amount($item->item_price) : 0.0;
                    $item->item_discount_amount = $item->item_discount_amount ? standardize_amount($item->item_discount_amount) : null;
                    $item->item_product_id      = $item->item_product_id ? $item->item_product_id : null;
                    $item->item_product_unit_id = $item->item_product_unit_id ? $item->item_product_unit_id : null;
                    $item->item_product_unit    = (new UnitsService())->getName($item->item_product_unit_id, $item->item_quantity);
                    $item_id                    = $item->item_id ?: null;
                    unset($item->item_id);
                    (new QuoteItemsService())->save($item_id, $item, $global_discount);
                } elseif (empty($item->item_name) && ( ! empty($item->item_quantity) || ! empty($item->item_price))) {
                    // Throw an error message and use the form validation for that (todo: where the translations of: The .* field is required.)
                    $this->load->library('form_validation');
                    $this->form_validation->set_rules('item_name', trans('item'), 'required');
                    $this->form_validation->run();
                    $response = ['success' => 0, 'validation_errors' => ['item_name' => form_error('item_name', '', '')]];
                    exit(json_encode($response));
                }
            }
            $quote_status_id = $this->input->post('quote_status_id');
            // Generate new quote number if needed
            $quote_number = $this->input->post('quote_number');
            if (empty($quote_number) && $quote_status_id != 1) {
                $quote_group_id = (new QuotesService())->getInvoiceGroupId($quote_id);
                $quote_number   = (new QuotesService())->getQuoteNumber($quote_group_id);
            }
            // Sometime global discount total value (round) need little adjust to be valid in ZugFerd2.3 standard
            if ( ! config_item('legacy_calculation') && $quote_discount_amount && $quote_discount_amount != $global_discount['item']) {
                // Adjust amount to reflect real calculation (cents)
                $quote_discount_amount = $global_discount['item'];
            }
            $db_array = ['quote_number' => $quote_number, 'quote_status_id' => $quote_status_id, 'quote_date_created' => date_to_mysql($this->input->post('quote_date_created')), 'quote_date_expires' => date_to_mysql($this->input->post('quote_date_expires')), 'quote_password' => $this->input->post('quote_password'), 'notes' => $this->input->post('notes'), 'quote_discount_amount' => standardize_amount($quote_discount_amount), 'quote_discount_percent' => standardize_amount($quote_discount_percent)];
            (new QuotesService())->save($quote_id, $db_array, $global_discount);
            if (config_item('legacy_calculation')) {
                // Recalculate for discounts
                (new QuoteAmountsService())->calculate($quote_id, $global_discount);
            }
            $response = ['success' => 1];
        } else {
            Log::error('980: I wasnt able to run the validation validation_rules_save_quote');
            $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        // Save all custom fields
        if ($this->input->post('custom')) {
            $db_array = [];
            $values   = [];
            foreach ($this->input->post('custom') as $custom) {
                if (preg_match('/^(.*)\[\]$/i', $custom['name'], $matches)) {
                    $values[$matches[1]][] = $custom['value'];
                } else {
                    $values[$custom['name']] = $custom['value'];
                }
            }
            foreach ($values as $key => $value) {
                preg_match('/^custom\[(.*?)\](?:\[\]|)$/', $key, $matches);
                if ($matches) {
                    $db_array[$matches[1]] = $value;
                }
            }
            $result = (new QuoteCustomService())->saveCustom($quote_id, $db_array);
            if ($result !== true) {
                $response = ['success' => 0, 'validation_errors' => $result];
                exit(json_encode($response));
            }
        }
        exit(json_encode($response));
    }

    /**
     * Validate quote tax rates and emit a JSON response indicating success or validation errors.
     *
     * If validation passes and the `legacy_calculation` configuration is enabled, the tax rates are saved.
     *
     * The JSON response has the shape:
     * - `success`: `1` on success, `0` on validation failure.
     * - `validation_errors`: present when `success` is `0`, containing validation error details.
     */
    public function saveQuoteTaxRate()
    {
        if ((new QuoteTaxRatesService())->runValidation()) {
            // Only Legacy calculation have global taxes - since v1.6.3
            config_item('legacy_calculation') && (new QuoteTaxRatesService())->save();
            $response = ['success' => 1];
        } else {
            $response = ['success' => 0, 'validation_errors' => (new QuoteTaxRatesService())->validation_errors];
        }
        exit(json_encode($response));
    }

    /**
         * Delete a quote item if the referenced quote exists and return a JSON success flag.
         *
         * If the provided quote exists (or no item_id is supplied), attempts to delete the posted
         * `item_id` and immediately outputs a JSON object with `success` set to `1` on successful
         * deletion or `0` otherwise, then exits execution.
         *
         * @param int $quote_id The ID of the quote used to verify existence before deleting the item.
         */
    public function deleteItem($quote_id)
    {
        $success = 0;
        $item_id = $this->input->post('item_id');
        // Only continue if the quote exists or no item id was provided
        if ((new QuotesService())->getById($quote_id) || empty($item_id)) {
            // Delete quote item
            $item = (new QuoteItemsService())->delete($item_id);
            // Check if deletion was successful
            if ($item) {
                $success = 1;
            }
        }
        // Return the response
        exit(json_encode(['success' => $success]));
    }

    /**
     * Retrieves a quote item identified by the POST field 'item_id' and outputs it as JSON.
     *
     * Reads 'item_id' from the HTTP POST payload, fetches the corresponding quote item via QuoteItemsService, encodes the result as JSON, and terminates execution.
     */
    public function getItem()
    {
        $item = (new QuoteItemsService())->getById($this->input->post('item_id'));
        exit(json_encode($item));
    }

    /**
     * Prepare and render the modal for copying a quote.
     *
     * Provides the view with invoice groups, tax rates, the requested quote and quote_id, and the specified client.
     */
    public function modalCopyQuote()
    {
        $this->load->module('layout');
        $data = ['invoice_groups' => (new InvoiceGroupsService())->get()->result(), 'tax_rates' => (new TaxRatesService())->get()->result(), 'quote_id' => $this->security->xss_clean($this->input->post('quote_id')), 'quote' => (new QuotesService())->where('ip_quotes.quote_id', $this->input->post('quote_id'))->get()->row(), 'client' => (new ClientsService())->getById($this->input->post('client_id'))];
        $this->layout->loadView('quotes/modal_copy_quote', $data);
    }

    /**
     * Copy an existing quote into a newly created quote and emit a JSON success or error response.
     *
     * Validates the incoming request, optionally adjusts the legacy_calculation config when einvoicing
     * is enabled, creates a new target quote, copies data from the source quote into the target,
     * and then outputs a JSON payload with either the new `quote_id` on success or validation errors.
     */
    public function copyQuote()
    {
        if ((new QuotesService())->runValidation()) {
            // Automatic calculation mode
            if (get_setting('einvoicing')) {
                // Shift to false (by default). Need true? See Dev Note on ipconfig example
                $this->config->set_item('legacy_calculation', ! empty($this->input->post('legacy_calculation')));
            }
            $target_id = (new QuotesService())->save();
            $source_id = $this->input->post('quote_id');
            (new QuotesService())->copyQuote($source_id, $target_id);
            $response = ['success' => 1, 'quote_id' => $target_id];
        } else {
            $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }

    /**
     * Prepares and renders the modal for changing the user assigned to a quote.
     *
     * Reads `user_id` and `quote_id` from POST input (sanitized), loads the latest users list,
     * and renders the `layout/ajax/modal_change_user_client` view with that data.
     */
    public function modalChangeUser()
    {
        $this->load->module('layout');
        $data = ['user_id' => $this->security->xss_clean($this->input->post('user_id')), 'quote_id' => $this->security->xss_clean($this->input->post('quote_id')), 'users' => (new UsersService())->getLatest()];
        $this->layout->loadView('layout/ajax/modal_change_user_client', $data);
    }

    /**
     * Change the user assigned to a quote and emit a JSON response indicating outcome.
     *
     * Validates that the provided user exists; if so, updates ip_quotes.user_id for the supplied
     * quote_id and returns a JSON success object containing the sanitized quote_id. If the user
     * is missing or invalid, returns a JSON object with validation errors.
     */
    public function changeUser()
    {
        // GetController the user ID
        $user_id = $this->security->xss_clean($this->input->post('user_id'));
        $user    = (new UsersService())->where('ip_users.user_id', $user_id)->get()->row();
        if ( ! empty($user)) {
            $quote_id = $this->input->post('quote_id');
            $db_array = ['user_id' => $user_id];
            $this->db->where('quote_id', $quote_id);
            $this->db->update('ip_quotes', $db_array);
            $response = ['success' => 1, 'quote_id' => $this->security->xss_clean($quote_id)];
        } else {
            $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }

    /**
     * Load the "change client" modal for a quote.
     *
     * Reads `client_id` and `quote_id` from POST (sanitized) and supplies them along with the latest clients list to the modal view.
     */
    public function modalChangeClient()
    {
        $this->load->module('layout');
        $data = ['client_id' => $this->security->xss_clean($this->input->post('client_id')), 'quote_id' => $this->security->xss_clean($this->input->post('quote_id')), 'clients' => (new ClientsService())->getLatest()];
        $this->layout->loadView('layout/ajax/modal_change_user_client', $data);
    }

    /**
     * Change the client associated with a quote.
     *
     * Validates that the posted client exists and, if so, updates the quote's client_id in the database using the posted quote_id.
     *
     * @return string
     *   JSON-encoded response: `{ "success": 1, "quote_id": "<cleaned_quote_id>" }` on success; `{ "success": 0, "validation_errors": { ... } }` if the client is invalid.
     */
    public function changeClient()
    {
        // GetController the client ID
        $client_id = $this->security->xss_clean($this->input->post('client_id'));
        $client    = (new ClientsService())->where('ip_clients.client_id', $client_id)->get()->row();
        if ( ! empty($client)) {
            $quote_id = $this->input->post('quote_id');
            $db_array = ['client_id' => $client_id];
            $this->db->where('quote_id', $quote_id);
            $this->db->update('ip_quotes', $db_array);
            $response = ['success' => 1, 'quote_id' => $this->security->xss_clean($quote_id)];
        } else {
            $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }

    /**
     * Prepare and render the "create quote" modal populated with necessary lookup data.
     *
     * Loads invoice groups, tax rates, the specified client (if provided), and recent clients,
     * then renders the quotes/modal_create_quote view with that data.
     */
    public function modalCreateQuote()
    {
        $this->load->module('layout');
        $data = ['invoice_groups' => (new InvoiceGroupsService())->get()->result(), 'tax_rates' => (new TaxRatesService())->get()->result(), 'client' => (new ClientsService())->getById($this->input->post('client_id')), 'clients' => (new ClientsService())->getLatest()];
        $this->layout->loadView('quotes/modal_create_quote', $data);
    }

    /**
     * Create a new quote from the current request and output a JSON response.
     *
     * On success, outputs JSON with `success` set to `1` and the created `quote_id`.
     * On validation failure, outputs JSON with `success` set to `0` and `validation_errors`.
     */
    public function create()
    {
        if ((new QuotesService())->runValidation()) {
            $quote_id = (new QuotesService())->create();
            $response = ['success' => 1, 'quote_id' => $quote_id];
        } else {
            $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }

    /**
     * Prepare and render the modal used to convert a quote into an invoice.
     *
     * Loads invoice groups and the specified quote, then renders the quote-to-invoice modal
     * with those values available to the view.
     *
     * @param int|string $quote_id The ID of the quote to convert.
     */
    public function modalQuoteToInvoice($quote_id)
    {
        $data = ['invoice_groups' => (new InvoiceGroupsService())->get()->result(), 'quote_id' => $this->security->xss_clean($quote_id), 'quote' => (new QuotesService())->where('ip_quotes.quote_id', $quote_id)->get()->row()];
        $this->load->view('quotes/modal_quote_to_invoice', $data);
    }

    /**
     * Converts a quote into a new invoice, copies quote items and tax rates to the invoice,
     * and updates discount and association fields accordingly.
     *
     * If validation succeeds, creates an invoice, applies the quote's discount values to the invoice,
     * links the invoice_id on the source quote, copies each quote item into the invoice (respecting
     * the computed global discount and current legacy_calculation setting), and copies quote tax rates
     * to the invoice. On validation failure, returns validation errors.
     *
     * The method sends a JSON response and terminates execution:
     * - on success: {"success":1,"invoice_id":<new_invoice_id>}
     * - on failure: {"success":0,"validation_errors":<errors>}
     */
    public function quoteToInvoice()
    {
        if ((new InvoicesService())->runValidation()) {
            // GetController the quote
            $quote_id = $this->input->post('quote_id');
            $quote    = (new QuotesService())->getById($quote_id);
            // Create new invoice
            $invoice_id = (new InvoicesService())->create(null, false);
            // Update the discounts
            $this->db->where('invoice_id', $invoice_id);
            $this->db->set('invoice_discount_amount', $quote->quote_discount_amount);
            $this->db->set('invoice_discount_percent', $quote->quote_discount_percent);
            $this->db->update('ip_invoices');
            // Save the invoice id to the quote
            $this->db->where('quote_id', $quote_id);
            $this->db->set('invoice_id', $invoice_id);
            $this->db->update('ip_quotes');
            // Discounts calculation - since v1.6.3 Need if taxes applied after discounts
            $global_discount = [
                'amount'  => $quote->quote_discount_amount,
                'percent' => $quote->quote_discount_percent,
                'item'    => 0.0,
                // Updated by ref (Need for quote_item_subtotal calculation in Mdl_quote_amounts)
                'items_subtotal' => (new QuoteItemsService())->getItemsSubtotal($quote->quote_id),
            ];
            unset($quote);
            // Free memory
            $quote_items = (new QuoteItemsService())->where('quote_id', $this->input->post('quote_id'))->get()->result();
            // Automatic calculation mode
            if (get_setting('einvoicing')) {
                // Shift to false (by default). Need true? See Dev Note on ipconfig example
                $this->config->set_item('legacy_calculation', ! empty($this->input->post('legacy_calculation')));
            }
            foreach ($quote_items as $quote_item) {
                $db_array = ['invoice_id' => $invoice_id, 'item_tax_rate_id' => $quote_item->item_tax_rate_id, 'item_product_id' => $quote_item->item_product_id, 'item_name' => $quote_item->item_name, 'item_description' => $quote_item->item_description, 'item_quantity' => $quote_item->item_quantity, 'item_price' => $quote_item->item_price, 'item_product_unit_id' => $quote_item->item_product_unit_id, 'item_product_unit' => $quote_item->item_product_unit, 'item_discount_amount' => $quote_item->item_discount_amount, 'item_order' => $quote_item->item_order];
                (new ItemsService())->save(null, $db_array, $global_discount);
            }
            $quote_tax_rates = (new QuoteTaxRatesService())->where('quote_id', $this->input->post('quote_id'))->get()->result();
            foreach ($quote_tax_rates as $quote_tax_rate) {
                $db_array = ['invoice_id' => $invoice_id, 'tax_rate_id' => $quote_tax_rate->tax_rate_id, 'include_item_tax' => $quote_tax_rate->include_item_tax, 'invoice_tax_rate_amount' => $quote_tax_rate->quote_tax_rate_amount];
                (new InvoiceTaxRatesService())->save(null, $db_array);
            }
            $response = ['success' => 1, 'invoice_id' => $invoice_id];
        } else {
            $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }
}