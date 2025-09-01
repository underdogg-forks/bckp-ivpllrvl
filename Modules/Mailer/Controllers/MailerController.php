<?php

namespace Modules\Mailer\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class MailerController extends AdminController
{
    private bool $mailer_configured;

    /**
     * MailerController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('mailer');
        $this->mailer_configured = mailer_configured();
        if ( ! $this->mailer_configured) {
            $this->layout->buffer('content', 'mailer/not_configured');
            $this->layout->render();
        }
    }

    /**
     * @originalName invoice
     *
     * @originalFile MailerController.php
     */
    public function invoice($invoice_id)
    {
        if ( ! $this->mailer_configured) {
            return;
        }
        $this->load->model(['email_templates/mdl_email_templates', 'custom_fields/mdl_custom_fields', 'invoices/mdl_templates', 'invoices/mdl_invoices', 'upload/mdl_uploads']);
        $this->load->helper(['template', 'dropzone']);
        $invoice           = (new InvoicesService())->getById($invoice_id);
        $email_template_id = select_email_invoice_template($invoice);
        $email_template    = '{}';
        if ($email_template_id) {
            $email_template = json_encode((new EmailTemplatesService())->getById($email_template_id));
        }
        // GetController all custom fields
        $custom_fields = [];
        foreach (array_keys((new CustomFieldsService())->customTables()) as $table) {
            $custom_fields[$table] = (new CustomFieldsService())->byTable($table)->get()->result();
        }

        return view('mailer.invoice', ['selected_email_template' => $email_template_id, 'selected_pdf_template' => select_pdf_invoice_template($invoice), 'email_templates' => (new EmailTemplatesService())->where('email_template_type', 'invoice')->get()->result(), 'email_template' => $email_template, 'custom_fields' => $custom_fields, 'pdf_templates' => (new TemplatesService())->getInvoiceTemplates(), 'invoice' => $invoice]);
    }

    /**
     * @originalName quote
     *
     * @originalFile MailerController.php
     */
    public function quote($quote_id)
    {
        if ( ! $this->mailer_configured) {
            return;
        }
        $this->load->model(['email_templates/mdl_email_templates', 'custom_fields/mdl_custom_fields', 'invoices/mdl_templates', 'quotes/mdl_quotes', 'upload/mdl_uploads']);
        $this->load->helper('dropzone');
        $email_template_id = get_setting('email_quote_template');
        $email_template    = '{}';
        if ($email_template_id) {
            $email_template = json_encode((new EmailTemplatesService())->getById($email_template_id));
        }
        // GetController all custom fields
        $custom_fields = [];
        foreach (array_keys((new CustomFieldsService())->customTables()) as $table) {
            $custom_fields[$table] = (new CustomFieldsService())->byTable($table)->get()->result();
        }

        return view('mailer.quote', ['selected_email_template' => $email_template_id, 'selected_pdf_template' => get_setting('pdf_quote_template'), 'email_templates' => (new EmailTemplatesService())->where('email_template_type', 'quote')->get()->result(), 'email_template' => $email_template, 'custom_fields' => $custom_fields, 'pdf_templates' => (new TemplatesService())->getQuoteTemplates(), 'quote' => (new QuotesService())->getById($quote_id)]);
    }

    /**
     * @originalName sendInvoice
     *
     * @originalFile MailerController.php
     */
    public function sendInvoice(string $invoice_id)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('invoices/view/' . $invoice_id);
        }
        if ( ! $this->mailer_configured) {
            return;
        }
        $to           = $this->input->post('to_email', true);
        $from         = $this->input->post('from_email', true);
        $from         = [$from, $this->input->post('from_name')];
        $pdf_template = $this->input->post('pdf_template', true);
        $subject      = $this->input->post('subject');
        $body         = $this->input->post('body');
        if (mb_strlen($body) != mb_strlen(strip_tags($body))) {
            $body = htmlspecialchars_decode($body, ENT_COMPAT);
        } else {
            $body = htmlspecialchars_decode(nl2br($body), ENT_COMPAT);
        }
        $cc  = $this->input->post('cc');
        $bcc = $this->input->post('bcc');
        $this->load->model('upload/mdl_uploads');
        $attachment_files = (new UploadsService())->getInvoiceUploads($invoice_id);
        (new InvoicesService())->generateInvoiceNumberIfApplicable($invoice_id);
        if (email_invoice($invoice_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files)) {
            (new InvoicesService())->markSent($invoice_id);
            $this->session->set_flashdata('alert_success', trans('email_successfully_sent'));
            redirect('invoices/view/' . $invoice_id);
        }
        redirect('mailer/invoice/' . $invoice_id);
    }

    /**
     * @originalName sendQuote
     *
     * @originalFile MailerController.php
     */
    public function sendQuote(string $quote_id)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('quotes/view/' . $quote_id);
        }
        if ( ! $this->mailer_configured) {
            return;
        }
        $to           = $this->input->post('to_email');
        $from         = $this->input->post('from_email');
        $from         = [$from, $this->input->post('from_name')];
        $pdf_template = $this->input->post('pdf_template');
        $subject      = $this->input->post('subject');
        if (mb_strlen($this->input->post('body')) != mb_strlen(strip_tags($this->input->post('body')))) {
            $body = htmlspecialchars_decode($this->input->post('body'), ENT_COMPAT);
        } else {
            $body = htmlspecialchars_decode(nl2br($this->input->post('body')), ENT_COMPAT);
        }
        $cc  = $this->input->post('cc');
        $bcc = $this->input->post('bcc');
        $this->load->model('upload/mdl_uploads');
        $attachment_files = (new UploadsService())->getQuoteUploads($quote_id);
        (new QuotesService())->generateQuoteNumberIfApplicable($quote_id);
        if (email_quote($quote_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files)) {
            (new QuotesService())->markSent($quote_id);
            $this->session->set_flashdata('alert_success', trans('email_successfully_sent'));
            redirect('quotes/view/' . $quote_id);
        }
        redirect('mailer/quote/' . $quote_id);
    }
}
