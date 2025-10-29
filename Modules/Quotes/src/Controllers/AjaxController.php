<?php

namespace Modules\Quotes\Controllers;

use AllowDynamicProperties;
use Illuminate\Support\Facades\Log;
use Modules\Core\Controllers\AdminController;
use Modules\Crm\app\Services\ClientsService;
use Modules\Invoices\app\Services\InvoiceGroupsService;
use Modules\Quotes\Services\QuoteAmountsService;
use Modules\Quotes\Services\QuoteItemsService;
use Modules\Quotes\Services\QuotesService;
use Modules\Quotes\Services\QuoteTaxRatesService;
use src\Services\TaxRatesService;
use src\Services\UnitsService;
use src\Services\UsersService;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    public $ajax_controller = true;

    /**
     * Persist a quote with its items, discounts, and custom fields and emit a JSON response indicating success or validation errors.
     *
     * Validates request input, saves or updates the quote and related items/tax/discount data, persists custom field values,
     * and outputs a JSON-encoded response before terminating execution.
     */
    public function save()
    {
        $quote_id = e(request()->input('quote_id', true));
        (new QuotesService())->setId($quote_id);
        if ((new QuotesService())->runValidation('validation_rules_save_quote')) {
            $items                  = json_decode(request()->input('items'));
            $quote_discount_percent = (float) request()->input('quote_discount_percent');
            $quote_discount_amount  = (float) request()->input('quote_discount_amount');
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
                config()->set('legacy_calculation', ! empty(request()->input('legacy_calculation')));
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
// TODO: Use Laravel services/facades - $this->load->library('form_validation');
                    // TODO: Move to Form Request - $this->form_validation->set_rules('item_name', trans('item'), 'required');
                    // TODO: Move to Form Request - $this->form_validation->run();
                    $response = ['success' => 0, 'validation_errors' => ['item_name' => form_error('item_name', '', '')]];
                    exit(json_encode($response));
                }
            }
            $quote_status_id = request()->input('quote_status_id');
            // Generate new quote number if needed
            $quote_number = request()->input('quote_number');
            if (empty($quote_number) && $quote_status_id != 1) {
                $quote_group_id = (new QuotesService())->getInvoiceGroupId($quote_id);
                $quote_number   = (new QuotesService())->getQuoteNumber($quote_group_id);
            }
            // Sometime global discount total value (round) need little adjust to be valid in ZugFerd2.3 standard
            if ( ! config_item('legacy_calculation') && $quote_discount_amount && $quote_discount_amount != $global_discount['item']) {
                // Adjust amount to reflect real calculation (cents)
                $quote_discount_amount = $global_discount['item'];
            }
            $db_array = ['quote_number' => $quote_number, 'quote_status_id' => $quote_status_id, 'quote_date_created' => date_to_mysql(request()->input('quote_date_created')), 'quote_date_expires' => date_to_mysql(request()->input('quote_date_expires')), 'quote_password' => request()->input('quote_password'), 'notes' => request()->input('notes'), 'quote_discount_amount' => standardize_amount($quote_discount_amount), 'quote_discount_percent' => standardize_amount($quote_discount_percent)];
            (new QuotesService())->save($quote_id, $db_array, $global_discount);
            if (config_item('legacy_calculation')) {
                // Recalculate for discounts
                (new QuoteAmountsService())->calculate($quote_id, $global_discount);
            }
            $response = ['success' => 1];
        } else {
            Log::error('980: I wasnt able to run the validation validation_rules_save_quote');
// TODO: Laravel autoloads helpers - $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        // Save all custom fields
        if (request()->input('custom')) {
            $db_array = [];
            $values   = [];
            foreach (request()->input('custom') as $custom) {
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
     * @param int $quote_id the ID of the quote used to verify existence before deleting the item
     */
    public function deleteItem($quote_id)
    {
        $success = 0;
        $item_id = request()->input('item_id');
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
     * Outputs the quote item identified by POST parameter 'item_id' as JSON.
     *
     * Fetches the quote item by its ID and sends a JSON-encoded representation to the client, then exits.
     */
    public function getItem()
    {
        $item = (new QuoteItemsService())->getById(request()->input('item_id'));
        exit(json_encode($item));
    }

    /**
     * Loads data required to copy an existing quote and renders the "copy quote" modal view.
     *
     * Prepares invoice groups, tax rates, the source quote, and the specified client, then loads
     * the view 'quotes/modal_copy_quote' with that data.
     *
     * Expects the following POST fields:
     * - 'quote_id': ID of the quote to copy.
     * - 'client_id': ID of the client to preselect in the modal.
     */
    public function modalCopyQuote()
    {
// TODO: Modules handled differently in Laravel - $this->load->module('layout');
        $data = ['invoice_groups' => (new InvoiceGroupsService())->getAll(), 'tax_rates' => (new TaxRatesService())->getAll(), 'quote_id' => e(request()->input('quote_id')), 'quote' => (new QuotesService())->getById(request()->input('quote_id')), 'client' => (new ClientsService())->getById(request()->input('client_id'))];
        $this->layout->loadView('quotes/modal_copy_quote', $data);
    }

    /**
     * Create a new quote by copying data from an existing quote and emit a JSON response.
     *
     * Validates the request, creates a target quote, copies the source quote's data into the target,
     * and outputs JSON with `{"success":1,"quote_id":<id>}` on success or `{"success":0,"validation_errors":...}` on failure.
     */
    public function copyQuote()
    {
        if ((new QuotesService())->runValidation()) {
            // Automatic calculation mode
            if (get_setting('einvoicing')) {
                // Shift to false (by default). Need true? See Dev Note on ipconfig example
                config()->set('legacy_calculation', ! empty(request()->input('legacy_calculation')));
            }
            $target_id = (new QuotesService())->save();
            $source_id = request()->input('quote_id');
            (new QuotesService())->copyQuote($source_id, $target_id);
            $response = ['success' => 1, 'quote_id' => $target_id];
        } else {
// TODO: Laravel autoloads helpers - $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }

    /**
     * Render the modal to change the user assigned to a quote.
     *
     * Reads sanitized `user_id` and `quote_id` from POST, loads the latest users, and renders
     * the `layout/ajax/modal_change_user_client` view with the prepared data.
     */
    public function modalChangeUser()
    {
// TODO: Modules handled differently in Laravel - $this->load->module('layout');
        $data = ['user_id' => e(request()->input('user_id')), 'quote_id' => e(request()->input('quote_id')), 'users' => (new UsersService())->getLatest()];
        $this->layout->loadView('layout/ajax/modal_change_user_client', $data);
    }

    /**
     * Change the user assigned to a quote based on POSTed input.
     *
     * Validates that the posted user exists; if valid, updates the quote's `user_id` in the database
     * and emits JSON containing a success flag and the sanitized `quote_id`. If the user is invalid
     * or missing, emits JSON containing a failure flag and validation error messages.
     */
    public function changeUser()
    {
        // GetController the user ID
        $user_id = e(request()->input('user_id'));
        $user    = (new UsersService())->getById($user_id);
        if ( ! empty($user)) {
            $quote_id = request()->input('quote_id');
            $db_array = ['user_id' => $user_id];
            $this->db->where('quote_id', $quote_id);
            $this->db->update('ip_quotes', $db_array);
            $response = ['success' => 1, 'quote_id' => e($quote_id)];
        } else {
// TODO: Laravel autoloads helpers - $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }

    /**
     * Render the modal used to change the client on an existing quote.
     *
     * Reads sanitized `client_id` and `quote_id` from POST and provides them,
     * along with the latest clients list, to the modal view.
     *
     * @return void
     */
    public function modalChangeClient()
    {
// TODO: Modules handled differently in Laravel - $this->load->module('layout');
        $data = ['client_id' => e(request()->input('client_id')), 'quote_id' => e(request()->input('quote_id')), 'clients' => (new ClientsService())->getLatest()];
        $this->layout->loadView('layout/ajax/modal_change_user_client', $data);
    }

    /**
     * Change the client associated with a quote.
     *
     * Validates that the posted client exists and, if so, updates the quote's client_id in the database using the posted quote_id.
     *
     * @return string
     *                JSON-encoded response: `{ "success": 1, "quote_id": "<cleaned_quote_id>" }` on success; `{ "success": 0, "validation_errors": { ... } }` if the client is invalid.
     */
    public function changeClient()
    {
        // GetController the client ID
        $client_id = e(request()->input('client_id'));
        $client    = (new ClientsService())->getById($client_id);
        if ( ! empty($client)) {
            $quote_id = request()->input('quote_id');
            $db_array = ['client_id' => $client_id];
            $this->db->where('quote_id', $quote_id);
            $this->db->update('ip_quotes', $db_array);
            $response = ['success' => 1, 'quote_id' => e($quote_id)];
        } else {
// TODO: Laravel autoloads helpers - $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }

    /**
     * Render the modal used to create a new quote for a client.
     *
     * Prepares view data keys:
     * - `invoice_groups`: all invoice groups
     * - `tax_rates`: all tax rates
     * - `client`: client identified by the POST field `client_id`
     * - `clients`: recent clients
     *
     * Loads the view `quotes/modal_create_quote` with the prepared data.
     */
    public function modalCreateQuote()
    {
// TODO: Modules handled differently in Laravel - $this->load->module('layout');
        $data = ['invoice_groups' => (new InvoiceGroupsService())->getAll(), 'tax_rates' => (new TaxRatesService())->getAll(), 'client' => (new ClientsService())->getById(request()->input('client_id')), 'clients' => (new ClientsService())->getLatest()];
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
// TODO: Laravel autoloads helpers - $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }

    /**
     * Render the modal for converting a specific quote to an invoice.
     *
     * Loads invoice groups and the quote identified by $quote_id and renders the
     * quotes/modal_quote_to_invoice view with the following view data:
     * - `invoice_groups`: list of invoice groups
     * - `quote_id`: sanitized quote identifier
     * - `quote`: the quote record
     *
     * @param int|string $quote_id the ID of the quote to convert
     */
    public function modalQuoteToInvoice($quote_id)
    {
        $data = ['invoice_groups' => (new InvoiceGroupsService())->getAll(), 'quote_id' => e($quote_id), 'quote' => (new QuotesService())->getById($quote_id)];
        return view('quotes/modal_quote_to_invoice', $data);
    }

    /**
     * Convert the specified quote into a new invoice, copying items, tax rates, discounts, and the quote→invoice association.
     *
     * Creates a new invoice, applies the source quote's global and per-item discounts to the invoice, copies each quote item into the invoice using the current legacy_calculation mode, copies quote tax rates to the invoice, and saves the created invoice_id on the source quote. Sends a JSON response and terminates execution:
     * - Success: {"success":1,"invoice_id":<new_invoice_id>}
     * - Failure: {"success":0,"validation_errors":<errors>}
     */
    public function quoteToInvoice()
    {
        if ((new InvoicesService())->runValidation()) {
            // GetController the quote
            $quote_id = request()->input('quote_id');
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
            $quote_items = (new QuoteItemsService())->getByQuoteId(request()->input('quote_id'));
            // Automatic calculation mode
            if (get_setting('einvoicing')) {
                // Shift to false (by default). Need true? See Dev Note on ipconfig example
                config()->set('legacy_calculation', ! empty(request()->input('legacy_calculation')));
            }
            foreach ($quote_items as $quote_item) {
                $db_array = ['invoice_id' => $invoice_id, 'item_tax_rate_id' => $quote_item->item_tax_rate_id, 'item_product_id' => $quote_item->item_product_id, 'item_name' => $quote_item->item_name, 'item_description' => $quote_item->item_description, 'item_quantity' => $quote_item->item_quantity, 'item_price' => $quote_item->item_price, 'item_product_unit_id' => $quote_item->item_product_unit_id, 'item_product_unit' => $quote_item->item_product_unit, 'item_discount_amount' => $quote_item->item_discount_amount, 'item_order' => $quote_item->item_order];
                (new ItemsService())->save(null, $db_array, $global_discount);
            }
            $quote_tax_rates = (new QuoteTaxRatesService())->getByQuoteId(request()->input('quote_id'));
            foreach ($quote_tax_rates as $quote_tax_rate) {
                $db_array = ['invoice_id' => $invoice_id, 'tax_rate_id' => $quote_tax_rate->tax_rate_id, 'include_item_tax' => $quote_tax_rate->include_item_tax, 'invoice_tax_rate_amount' => $quote_tax_rate->quote_tax_rate_amount];
                (new InvoiceTaxRatesService())->save(null, $db_array);
            }
            $response = ['success' => 1, 'invoice_id' => $invoice_id];
        } else {
// TODO: Laravel autoloads helpers - $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }
}
