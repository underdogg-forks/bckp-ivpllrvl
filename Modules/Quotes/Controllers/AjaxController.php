<?php

namespace Modules\Quotes\Controllers;

use Illuminate\Support\Facades\Log;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    public $ajax_controller = true;

    /**
     * @originalName save
     *
     * @originalFile AjaxController.php
     */
    public function save()
    {
        $this->load->model(['quotes/mdl_quote_items', 'quotes/mdl_quotes', 'units/mdl_units']);
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
                $this->load->model('quotes/mdl_quote_amounts');
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
            $this->load->model('custom_fields/mdl_quote_custom');
            $result = (new QuoteCustomService())->saveCustom($quote_id, $db_array);
            if ($result !== true) {
                $response = ['success' => 0, 'validation_errors' => $result];
                exit(json_encode($response));
            }
        }
        exit(json_encode($response));
    }

    /**
     * @originalName saveQuoteTaxRate
     *
     * @originalFile AjaxController.php
     */
    public function saveQuoteTaxRate()
    {
        $this->load->model('quotes/mdl_quote_tax_rates');
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
     * @originalName deleteItem
     *
     * @originalFile AjaxController.php
     */
    public function deleteItem($quote_id)
    {
        $success = 0;
        $item_id = $this->input->post('item_id');
        $this->load->model('mdl_quotes');
        // Only continue if the quote exists or no item id was provided
        if ((new QuotesService())->getById($quote_id) || empty($item_id)) {
            // Delete quote item
            $this->load->model('mdl_quote_items');
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
     * @originalName getItem
     *
     * @originalFile AjaxController.php
     */
    public function getItem()
    {
        $this->load->model('quotes/mdl_quote_items');
        $item = (new QuoteItemsService())->getById($this->input->post('item_id'));
        exit(json_encode($item));
    }

    /**
     * @originalName modalCopyQuote
     *
     * @originalFile AjaxController.php
     */
    public function modalCopyQuote()
    {
        $this->load->module('layout');
        $this->load->model(['quotes/mdl_quotes', 'invoice_groups/mdl_invoice_groups', 'tax_rates/mdl_tax_rates', 'clients/mdl_clients']);
        $data = ['invoice_groups' => (new InvoiceGroupsService())->get()->result(), 'tax_rates' => (new TaxRatesService())->get()->result(), 'quote_id' => $this->security->xss_clean($this->input->post('quote_id')), 'quote' => (new QuotesService())->where('ip_quotes.quote_id', $this->input->post('quote_id'))->get()->row(), 'client' => (new ClientsService())->getById($this->input->post('client_id'))];
        $this->layout->loadView('quotes/modal_copy_quote', $data);
    }

    /**
     * @originalName copyQuote
     *
     * @originalFile AjaxController.php
     */
    public function copyQuote()
    {
        $this->load->model(['quotes/mdl_quotes', 'quotes/mdl_quote_items', 'quotes/mdl_quote_tax_rates']);
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
     * @originalName modalChangeUser
     *
     * @originalFile AjaxController.php
     */
    public function modalChangeUser()
    {
        $this->load->module('layout');
        $this->load->model('users/mdl_users');
        $data = ['user_id' => $this->security->xss_clean($this->input->post('user_id')), 'quote_id' => $this->security->xss_clean($this->input->post('quote_id')), 'users' => (new UsersService())->getLatest()];
        $this->layout->loadView('layout/ajax/modal_change_user_client', $data);
    }

    /**
     * @originalName changeUser
     *
     * @originalFile AjaxController.php
     */
    public function changeUser()
    {
        $this->load->model(['quotes/mdl_quotes', 'users/mdl_users']);
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
     * @originalName modalChangeClient
     *
     * @originalFile AjaxController.php
     */
    public function modalChangeClient()
    {
        $this->load->module('layout');
        $this->load->model('clients/mdl_clients');
        $data = ['client_id' => $this->security->xss_clean($this->input->post('client_id')), 'quote_id' => $this->security->xss_clean($this->input->post('quote_id')), 'clients' => (new ClientsService())->getLatest()];
        $this->layout->loadView('layout/ajax/modal_change_user_client', $data);
    }

    /**
     * @originalName changeClient
     *
     * @originalFile AjaxController.php
     */
    public function changeClient()
    {
        $this->load->model(['quotes/mdl_quotes', 'clients/mdl_clients']);
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
     * @originalName modalCreateQuote
     *
     * @originalFile AjaxController.php
     */
    public function modalCreateQuote()
    {
        $this->load->module('layout');
        $this->load->model(['invoice_groups/mdl_invoice_groups', 'tax_rates/mdl_tax_rates', 'clients/mdl_clients']);
        $data = ['invoice_groups' => (new InvoiceGroupsService())->get()->result(), 'tax_rates' => (new TaxRatesService())->get()->result(), 'client' => (new ClientsService())->getById($this->input->post('client_id')), 'clients' => (new ClientsService())->getLatest()];
        $this->layout->loadView('quotes/modal_create_quote', $data);
    }

    /**
     * @originalName create
     *
     * @originalFile AjaxController.php
     */
    public function create()
    {
        $this->load->model('quotes/mdl_quotes');
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
     * @originalName modalQuoteToInvoice
     *
     * @originalFile AjaxController.php
     */
    public function modalQuoteToInvoice($quote_id)
    {
        $this->load->model(['invoice_groups/mdl_invoice_groups', 'quotes/mdl_quotes']);
        $data = ['invoice_groups' => (new InvoiceGroupsService())->get()->result(), 'quote_id' => $this->security->xss_clean($quote_id), 'quote' => (new QuotesService())->where('ip_quotes.quote_id', $quote_id)->get()->row()];
        $this->load->view('quotes/modal_quote_to_invoice', $data);
    }

    /**
     * @originalName quoteToInvoice
     *
     * @originalFile AjaxController.php
     */
    public function quoteToInvoice()
    {
        $this->load->model(['invoices/mdl_invoices', 'invoices/mdl_items', 'invoices/mdl_invoice_tax_rates', 'quotes/mdl_quotes', 'quotes/mdl_quote_items', 'quotes/mdl_quote_tax_rates']);
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
