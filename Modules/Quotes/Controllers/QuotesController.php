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
        redirect()->route('quotes/status/all');
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
                (new QuotesService())->isDraft();
                break;
            case 'sent':
                (new QuotesService())->isSent();
                break;
            case 'viewed':
                (new QuotesService())->isViewed();
                break;
            case 'approved':
                (new QuotesService())->isApproved();
                break;
            case 'rejected':
                (new QuotesService())->isRejected();
                break;
            case 'canceled':
                (new QuotesService())->isCanceled();
                break;
        }
        (new QuotesService())->paginate(site_url('quotes/status/' . $status), $page);
        $quotes = (new QuotesService())->result();

        return view('quotes.index', ['quotes' => $quotes, 'status' => $status, 'filter_display' => true, 'filter_placeholder' => trans('filter_quotes'), 'filter_method' => 'filter_quotes', 'quote_statuses' => (new QuotesService())->statuses()]);
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
        $fields = (new QuoteCustomService())->byId($quote_id)->get()->result();
        $this->db->reset_query();
        $quote_custom = (new QuoteCustomService())->where('quote_id', $quote_id)->get();
        if ($quote_custom->numRows()) {
            $quote_custom = $quote_custom->row();
            unset($quote_custom->quote_id, $quote_custom->quote_custom_id);
            foreach ($quote_custom as $key => $val) {
                (new QuotesService())->setFormValue('custom[' . $key . ']', $val);
            }
        }
        $quote = (new QuotesService())->getById($quote_id);
        if ( ! $quote) {
            show_404();
        }
        $custom_fields = (new CustomFieldsService())->byTable('ip_quote_custom')->get()->result();
        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, (new CustomValuesService())->customValueFields())) {
                $values                                        = (new CustomValuesService())->getByFid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }
        foreach ($custom_fields as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->quote_custom_fieldid == $cfield->custom_field_id) {
                    // TODO: Hackish, may need a better optimization
                    (new QuotesService())->setFormValue('custom[' . $cfield->custom_field_id . ']', $fvalue->quote_custom_fieldvalue);
                    break;
                }
            }
        }
        $items = (new QuoteItemsService())->where('quote_id', $quote_id)->get()->result();
        // GetController eInvoice library name and user checks
        $einvoice = get_einvoice_usage($quote, $items);
        // Activate 'Change_user' if admin users > 1  (get the sum of user type = 1 & active)
        $change_user = $this->db->from('ip_users')->where(['user_type' => 1, 'user_active' => 1])->select_sum('user_type')->get()->row();
        $change_user = $change_user->user_type > 1;
        $this->layout->set(['quote' => $quote, 'items' => $items, 'quote_id' => $quote_id, 'einvoice' => $einvoice, 'change_user' => $change_user, 'units' => (new UnitsService())->get()->result(), 'tax_rates' => (new TaxRatesService())->get()->result(), 'quote_tax_rates' => (new QuoteTaxRatesService())->where('quote_id', $quote_id)->get()->result(), 'quote_statuses' => (new QuotesService())->statuses(), 'custom_fields' => $custom_fields, 'custom_values' => $custom_values, 'custom_js_vars' => ['currency_symbol' => get_setting('currency_symbol'), 'currency_symbol_placement' => get_setting('currency_symbol_placement'), 'decimal_point' => get_setting('decimal_point')], 'legacy_calculation' => config_item('legacy_calculation')]);
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
        (new QuotesService())->delete($quote_id);
        // Redirect to quote index
        redirect()->route('quotes/index');
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
            (new QuotesService())->generateQuoteNumberIfApplicable($quote_id);
            (new QuotesService())->markSent($quote_id);
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
        (new QuoteTaxRatesService())->delete($quote_tax_rate_id);
        $this->load->model('quotes/mdl_quote_amounts');
        $global_discount['item'] = (new QuoteAmountsService())->getGlobalDiscount($quote_id);
        // Recalculate quote amounts
        (new QuoteAmountsService())->calculate($quote_id, $global_discount);
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
            $global_discount['item'] = (new QuoteAmountsService())->getGlobalDiscount($quote_id->quote_id);
            // Recalculate quote amounts
            (new QuoteAmountsService())->calculate($quote_id->quote_id, $global_discount);
        }
    }
}
