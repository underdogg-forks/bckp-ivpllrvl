<?php

namespace Modules\Guest\Controllers;

use Illuminate\Support\Facades\Log;

use AllowDynamicProperties;
use Modules\Core\Controllers\GuestController as BaseGuestController;

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
        if ( ! $invoice_url_key) {
            show_404();
        }
        $this->load->model('invoices/mdl_invoices');
        $invoice = (new InvoicesService())->guestVisible()->where('invoice_url_key', $invoice_url_key)->get();
        if ($invoice->numRows() != 1) {
            show_404();
        }
        $this->load->model(['invoices/mdl_items', 'invoices/mdl_invoice_tax_rates', 'payment_methods/mdl_payment_methods', 'custom_fields/mdl_custom_fields', 'upload/mdl_uploads']);
        $this->load->helper('template');
        $invoice = $invoice->row();
        if ($this->session->userdata('user_type') != 1 && $invoice->invoice_status_id == 2) {
            (new InvoicesService())->markViewed($invoice->invoice_id);
        }
        $payment_method = (new PaymentMethodsService())->where('payment_method_id', $invoice->payment_method)->get()->row();
        if ($invoice->payment_method == 0) {
            $payment_method = null;
        }
        // GetController all custom fields
        $custom_fields = ['invoice' => (new CustomFieldsService())->getValuesForFields('mdl_invoice_custom', $invoice->invoice_id), 'client' => (new CustomFieldsService())->getValuesForFields('mdl_client_custom', $invoice->client_id), 'user' => (new CustomFieldsService())->getValuesForFields('mdl_user_custom', $invoice->user_id)];
        // Attachments
        $attachments = $this->getAttachments($invoice_url_key);
        $is_overdue  = $invoice->invoice_balance > 0 && strtotime($invoice->invoice_date_due) < time();
        $data        = ['invoice' => $invoice, 'items' => (new ItemsService())->where('invoice_id', $invoice->invoice_id)->get()->result(), 'invoice_tax_rates' => (new InvoiceTaxRatesService())->where('invoice_id', $invoice->invoice_id)->get()->result(), 'invoice_url_key' => $invoice_url_key, 'flash_message' => $this->session->flashdata('flash_message'), 'payment_method' => $payment_method, 'is_overdue' => $is_overdue, 'attachments' => $attachments, 'custom_fields' => $custom_fields, 'legacy_calculation' => config_item('legacy_calculation')];
        $this->load->view('invoice_templates/public/' . get_setting('public_invoice_template') . '.php', $data);
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
        if ( ! $quote_url_key) {
            show_404();
        }
        $this->load->model('quotes/mdl_quotes');
        $quote = (new QuotesService())->guestVisible()->where('quote_url_key', $quote_url_key)->get();
        if ($quote->numRows() != 1) {
            show_404();
        }
        $this->load->model('quotes/mdl_quote_items');
        $this->load->model('quotes/mdl_quote_tax_rates');
        $this->load->model('custom_fields/mdl_custom_fields');
        $quote = $quote->row();
        if ($this->session->userdata('user_type') != 1 && $quote->quote_status_id == 2) {
            (new QuotesService())->markViewed($quote->quote_id);
        }
        // GetController all custom fields
        $custom_fields = ['quote' => (new CustomFieldsService())->getValuesForFields('mdl_quote_custom', $quote->quote_id), 'client' => (new CustomFieldsService())->getValuesForFields('mdl_client_custom', $quote->client_id), 'user' => (new CustomFieldsService())->getValuesForFields('mdl_user_custom', $quote->user_id)];
        // Attachments
        $attachments = $this->getAttachments($quote_url_key);
        $is_expired  = strtotime($quote->quote_date_expires) < time();
        $data        = ['quote' => $quote, 'items' => (new QuoteItemsService())->where('quote_id', $quote->quote_id)->get()->result(), 'quote_tax_rates' => (new QuoteTaxRatesService())->where('quote_id', $quote->quote_id)->get()->result(), 'quote_url_key' => $quote_url_key, 'flash_message' => $this->session->flashdata('flash_message'), 'is_expired' => $is_expired, 'attachments' => $attachments, 'custom_fields' => $custom_fields, 'legacy_calculation' => config_item('legacy_calculation')];
        $this->load->view('quote_templates/public/' . get_setting('public_quote_template') . '.php', $data);
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
        $this->load->helper('mailer');
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
        $this->load->helper('mailer');
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
        $query = $this->db->query("SELECT file_name_new,file_name_original FROM ip_uploads WHERE url_key = '" . $url_key . "'");
        $names = [];
        if ($query->numRows() > 0) {
            foreach ($query->result() as $row) {
                $names[] = ['name' => $row->file_name_original, 'fullname' => $row->file_name_new, 'size' => filesize(UPLOADS_CFILES_FOLDER . $row->file_name_new)];
            }
        }

        return $names;
    }
}
