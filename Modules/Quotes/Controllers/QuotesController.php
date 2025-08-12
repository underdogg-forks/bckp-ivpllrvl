<?php

namespace Modules\Quotes\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class QuotesController extends AdminController
{
    /**
     * QuotesController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_quotes');
    }

    /**
     * @originalName index
     *
     * @originalFile QuotesController.php
     */
    public function index()
    {
        // Display all quotes by default
        redirect('quotes/status/all');
    }

    /**
     * @originalName status
     *
     * @originalFile QuotesController.php
     */
    public function status(string $status = 'all', $page = 0)
    {
        // Determine which group of quotes to load
        switch ($status) {
            case 'draft':
                $this->mdl_quotes->isDraft();
                break;
            case 'sent':
                $this->mdl_quotes->isSent();
                break;
            case 'viewed':
                $this->mdl_quotes->isViewed();
                break;
            case 'approved':
                $this->mdl_quotes->isApproved();
                break;
            case 'rejected':
                $this->mdl_quotes->isRejected();
                break;
            case 'canceled':
                $this->mdl_quotes->isCanceled();
                break;
        }
        $this->mdl_quotes->paginate(site_url('quotes/status/' . $status), $page);
        $quotes = $this->mdl_quotes->result();
        $this->layout->set(['quotes' => $quotes, 'status' => $status, 'filter_display' => true, 'filter_placeholder' => trans('filter_quotes'), 'filter_method' => 'filter_quotes', 'quote_statuses' => $this->mdl_quotes->statuses()]);
        $this->layout->buffer('content', 'quotes/index');
        $this->layout->render();
    }

    /**
     * @originalName view
     *
     * @originalFile QuotesController.php
     */
    public function view($quote_id)
    {
        $this->load->model(['quotes/mdl_quote_items', 'tax_rates/mdl_tax_rates', 'units/mdl_units', 'mdl_quote_tax_rates', 'custom_fields/mdl_custom_fields', 'custom_values/mdl_custom_values', 'custom_fields/mdl_quote_custom', 'upload/mdl_uploads']);
        $this->load->helper(['custom_values', 'dropzone', 'e-invoice']);
        $fields = $this->mdl_quote_custom->byId($quote_id)->get()->result();
        $this->db->reset_query();
        $quote_custom = $this->mdl_quote_custom->where('quote_id', $quote_id)->get();
        if ($quote_custom->numRows()) {
            $quote_custom = $quote_custom->row();
            unset($quote_custom->quote_id, $quote_custom->quote_custom_id);
            foreach ($quote_custom as $key => $val) {
                $this->mdl_quotes->setFormValue('custom[' . $key . ']', $val);
            }
        }
        $quote = $this->mdl_quotes->getById($quote_id);
        if ( ! $quote) {
            show_404();
        }
        $custom_fields = $this->mdl_custom_fields->byTable('ip_quote_custom')->get()->result();
        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, $this->mdl_custom_values->customValueFields())) {
                $values                                        = $this->mdl_custom_values->getByFid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }
        foreach ($custom_fields as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->quote_custom_fieldid == $cfield->custom_field_id) {
                    // TODO: Hackish, may need a better optimization
                    $this->mdl_quotes->setFormValue('custom[' . $cfield->custom_field_id . ']', $fvalue->quote_custom_fieldvalue);
                    break;
                }
            }
        }
        $items = $this->mdl_quote_items->where('quote_id', $quote_id)->get()->result();
        // GetController eInvoice library name and user checks
        $einvoice = get_einvoice_usage($quote, $items);
        // Activate 'Change_user' if admin users > 1  (get the sum of user type = 1 & active)
        $change_user = $this->db->from('ip_users')->where(['user_type' => 1, 'user_active' => 1])->select_sum('user_type')->get()->row();
        $change_user = $change_user->user_type > 1;
        $this->layout->set(['quote' => $quote, 'items' => $items, 'quote_id' => $quote_id, 'einvoice' => $einvoice, 'change_user' => $change_user, 'units' => $this->mdl_units->get()->result(), 'tax_rates' => $this->mdl_tax_rates->get()->result(), 'quote_tax_rates' => $this->mdl_quote_tax_rates->where('quote_id', $quote_id)->get()->result(), 'quote_statuses' => $this->mdl_quotes->statuses(), 'custom_fields' => $custom_fields, 'custom_values' => $custom_values, 'custom_js_vars' => ['currency_symbol' => get_setting('currency_symbol'), 'currency_symbol_placement' => get_setting('currency_symbol_placement'), 'decimal_point' => get_setting('decimal_point')], 'legacy_calculation' => config_item('legacy_calculation')]);
        $this->layout->buffer([['modal_delete_quote', 'quotes/modal_delete_quote'], ['modal_add_quote_tax', 'quotes/modal_add_quote_tax'], ['content', 'quotes/view']]);
        $this->layout->render();
    }

    /**
     * @originalName delete
     *
     * @originalFile QuotesController.php
     */
    public function delete($quote_id)
    {
        // Delete the quote
        $this->mdl_quotes->delete($quote_id);
        // Redirect to quote index
        redirect('quotes/index');
    }

    /**
     * @originalName generatePdf
     *
     * @originalFile QuotesController.php
     */
    public function generatePdf($quote_id, $stream = true, $quote_template = null)
    {
        $this->load->helper('pdf');
        if (get_setting('mark_quotes_sent_pdf') == 1) {
            $this->mdl_quotes->generateQuoteNumberIfApplicable($quote_id);
            $this->mdl_quotes->markSent($quote_id);
        }
        generate_quote_pdf($quote_id, $stream, $quote_template);
    }

    /**
     * @originalName deleteQuoteTax
     *
     * @originalFile QuotesController.php
     */
    public function deleteQuoteTax(string $quote_id, $quote_tax_rate_id)
    {
        $this->load->model('quotes/mdl_quote_tax_rates');
        $this->mdl_quote_tax_rates->delete($quote_tax_rate_id);
        $this->load->model('quotes/mdl_quote_amounts');
        $global_discount['item'] = $this->mdl_quote_amounts->getGlobalDiscount($quote_id);
        // Recalculate quote amounts
        $this->mdl_quote_amounts->calculate($quote_id, $global_discount);
        redirect('quotes/view/' . $quote_id);
    }

    /**
     * @originalName recalculateAllQuotes
     *
     * @originalFile QuotesController.php
     */
    public function recalculateAllQuotes()
    {
        $this->db->select('quote_id');
        $quote_ids = $this->db->get('ip_quotes')->result();
        $this->load->model('mdl_quote_amounts');
        foreach ($quote_ids as $quote_id) {
            $global_discount['item'] = $this->mdl_quote_amounts->getGlobalDiscount($quote_id->quote_id);
            // Recalculate quote amounts
            $this->mdl_quote_amounts->calculate($quote_id->quote_id, $global_discount);
        }
    }
}
