<?php

namespace Modules\Guest\Controllers;

use AllowDynamicProperties;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Modules\Core\Controllers\GuestController as BaseGuestController;
use Modules\CustomFields\Services\CustomFieldsService;
use Modules\Invoices\Services\InvoicesService;
use Modules\Invoices\Services\InvoiceTaxRatesService;
use Modules\Invoices\Services\ItemsService;
use Modules\PaymentMethods\Services\PaymentMethodsService;
use Modules\Quotes\Services\QuoteItemsService;
use Modules\Quotes\Services\QuotesService;
use Modules\Quotes\Services\QuoteTaxRatesService;

#[AllowDynamicProperties]
class View extends BaseGuestController
{
    /**
     * @originalName invoice
     *
     * @originalFile View.php
     */
    public function invoice($invoice_url_key = '')
    {
        if (! $invoice_url_key) {
            abort(404);
        }
        $invoice = (new InvoicesService())->guestVisible()->where('invoice_url_key', $invoice_url_key)->get();
        if ($invoice->numRows() != 1) {
            abort(404);
        }
        $invoice = $invoice->row();
        if (Session::get('user_type') != 1 && $invoice->invoice_status_id == 2) {
            (new InvoicesService())->markViewed($invoice->invoice_id);
        }
        $payment_method = (new PaymentMethodsService())->where('payment_method_id', $invoice->payment_method)->get()->row();
        if ($invoice->payment_method == 0) {
            $payment_method = null;
        }
        $custom_fields = [
            'invoice' => (new CustomFieldsService())->getValuesForFields('mdl_invoice_custom', $invoice->invoice_id),
            'client' => (new CustomFieldsService())->getValuesForFields('mdl_client_custom', $invoice->client_id),
            'user' => (new CustomFieldsService())->getValuesForFields('mdl_user_custom', $invoice->user_id),
        ];
        $attachments = $this->getAttachments($invoice_url_key);
        $is_overdue  = $invoice->invoice_balance > 0 && strtotime($invoice->invoice_date_due) < time();
        $data        = [
            'invoice' => $invoice,
            'items' => (new ItemsService())->where('invoice_id', $invoice->invoice_id)->get()->result(),
            'invoice_tax_rates' => (new InvoiceTaxRatesService())->where('invoice_id', $invoice->invoice_id)->get()->result(),
            'invoice_url_key' => $invoice_url_key,
            'flash_message' => Session::get('flash_message'),
            'payment_method' => $payment_method,
            'is_overdue' => $is_overdue,
            'attachments' => $attachments,
            'custom_fields' => $custom_fields,
            'legacy_calculation' => Config::get('legacy_calculation'),
        ];
        return view('invoice_templates.public.' . Config::get('public_invoice_template'), $data);
    }

    /**
     * @originalName generateInvoicePdf
     *
     * @originalFile View.php
     */
    public function generateInvoicePdf($invoice_url_key, $stream = true, $invoice_template = null)
    {
        $this->load->model('invoices/mdl_invoices');
        $invoice = (new InvoicesService())->guestVisible()->where('invoice_url_key', $invoice_url_key)->get();
        if ($invoice->numRows() == 1) {
            $invoice = $invoice->row();
            if ( ! $invoice_template) {
                $this->load->helper('template');
                $invoice_template = select_pdf_invoice_template($invoice);
            }
            $this->load->helper('pdf');
            generate_invoice_pdf($invoice->invoice_id, $stream, $invoice_template, 1);
        }
    }

    /**
     * @originalName generateSumexPdf
     *
     * @originalFile View.php
     */
    public function generateSumexPdf($invoice_url_key, $stream = true, $invoice_template = null)
    {
        $this->load->model('invoices/mdl_invoices');
        $invoice = (new InvoicesService())->guestVisible()->where('invoice_url_key', $invoice_url_key)->get();
        if ($invoice->numRows() == 1) {
            $invoice = $invoice->row();
            if ($invoice->sumex_id == null) {
                show_404();
            }
            if ( ! $invoice_template) {
                $invoice_template = get_setting('pdf_invoice_template');
            }
            $this->load->helper('pdf');
            generate_invoice_sumex($invoice->invoice_id);
        }
    }

    /**
     * @originalName quote
     *
     * @originalFile View.php
     */
    public function quote($quote_url_key = '')
    {
        if (! $quote_url_key) {
            abort(404);
        }
        $quote = (new QuotesService())->guestVisible()->where('quote_url_key', $quote_url_key)->get();
        if ($quote->numRows() != 1) {
            abort(404);
        }
        $quote = $quote->row();
        if (Session::get('user_type') != 1 && $quote->quote_status_id == 2) {
            (new QuotesService())->markViewed($quote->quote_id);
        }
        $custom_fields = [
            'quote' => (new CustomFieldsService())->getValuesForFields('mdl_quote_custom', $quote->quote_id),
            'client' => (new CustomFieldsService())->getValuesForFields('mdl_client_custom', $quote->client_id),
            'user' => (new CustomFieldsService())->getValuesForFields('mdl_user_custom', $quote->user_id),
        ];
        $attachments = $this->getAttachments($quote_url_key);
        $is_expired  = strtotime($quote->quote_date_expires) < time();
        $data        = [
            'quote' => $quote,
            'items' => (new QuoteItemsService())->where('quote_id', $quote->quote_id)->get()->result(),
            'quote_tax_rates' => (new QuoteTaxRatesService())->where('quote_id', $quote->quote_id)->get()->result(),
            'quote_url_key' => $quote_url_key,
            'flash_message' => Session::get('flash_message'),
            'is_expired' => $is_expired,
            'attachments' => $attachments,
            'custom_fields' => $custom_fields,
            'legacy_calculation' => Config::get('legacy_calculation'),
        ];
        return view('quote_templates.public.' . Config::get('public_quote_template'), $data);
    }

    /**
     * @originalName generateQuotePdf
     *
     * @originalFile View.php
     */
    public function generateQuotePdf($quote_url_key, $stream = true, $quote_template = null)
    {
        $this->load->model('quotes/mdl_quotes');
        $quote = (new QuotesService())->guestVisible()->where('quote_url_key', $quote_url_key)->get()->row();
        if ( ! $quote) {
            show_404();
        }
        if ( ! $quote_template) {
            $quote_template = get_setting('pdf_quote_template');
        }
        $this->load->helper('pdf');
        generate_quote_pdf($quote->quote_id, $stream, $quote_template);
    }

    /**
     * @originalName approveQuote
     *
     * @originalFile View.php
     */
    public function approveQuote(string $quote_url_key)
    {
        $this->load->model('quotes/mdl_quotes');
        (new QuotesService())->approveQuoteByKey($quote_url_key);
        email_quote_status((new QuotesService())->where('ip_quotes.quote_url_key', $quote_url_key)->get()->row()->quote_id, 'approved');
        redirect('guest/view/quote/' . $quote_url_key);
    }

    /**
     * @originalName rejectQuote
     *
     * @originalFile View.php
     */
    public function rejectQuote(string $quote_url_key)
    {
        $this->load->model('quotes/mdl_quotes');
        (new QuotesService())->rejectQuoteByKey($quote_url_key);
        email_quote_status((new QuotesService())->where('ip_quotes.quote_url_key', $quote_url_key)->get()->row()->quote_id, 'rejected');
        redirect('guest/view/quote/' . $quote_url_key);
    }

    /**
     * @originalName getAttachments
     *
     * @originalFile View.php
     */
    private function getAttachments(string $url_key): array
    {
        $results = DB::table('ip_uploads')->select('file_name_new', 'file_name_original')->where('url_key', $url_key)->get();
        $names = [];
        foreach ($results as $row) {
            $names[] = [
                'name' => $row->file_name_original,
                'fullname' => $row->file_name_new,
                'size' => file_exists(storage_path('app/uploads/' . $row->file_name_new)) ? filesize(storage_path('app/uploads/' . $row->file_name_new)) : 0,
            ];
        }

        return $names;
    }
}
