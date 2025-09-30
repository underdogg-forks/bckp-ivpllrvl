<?php

namespace Modules\Mailer\Controllers;

use AllowDynamicProperties;
use App\Helpers\MailerHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Modules\Core\Controllers\AdminController;
use Modules\CustomFields\Services\CustomFieldsService;
use Modules\EmailTemplates\Services\EmailTemplatesService;
use Modules\Invoices\Services\InvoicesService;
use Modules\Invoices\Services\TemplatesService;
use Modules\Quotes\Services\QuotesService;
use Modules\Upload\Services\UploadsService;

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
        $this->mailer_configured = MailerHelper::mailerConfigured();
        if (! $this->mailer_configured) {
            abort(response()->view('mailer.not_configured'), 503);
        }
    }

    /**
     * @originalName invoice
     *
     * @originalFile MailerController.php
     */
    public function invoice(Request $request, int $invoice_id)
    {
        if (! $this->mailer_configured) {
            return;
        }
        $invoice           = (new InvoicesService())->getById($invoice_id);
        $email_template_id = select_email_invoice_template($invoice);
        $email_template    = '{}';
        if ($email_template_id) {
            $email_template = json_encode((new EmailTemplatesService())->getById($email_template_id));
        }
        $custom_fields = [];
        foreach (array_keys((new CustomFieldsService())->customTables()) as $table) {
            $custom_fields[$table] = (new CustomFieldsService())->byTable($table)->get()->result();
        }
        return view('mailer.invoice', [
            'selected_email_template' => $email_template_id,
            'selected_pdf_template' => select_pdf_invoice_template($invoice),
            'email_templates' => (new EmailTemplatesService())->where('email_template_type', 'invoice')->get()->result(),
            'email_template' => $email_template,
            'custom_fields' => $custom_fields,
            'pdf_templates' => (new TemplatesService())->getInvoiceTemplates(),
            'invoice' => $invoice,
        ]);
    }

    /**
     * @originalName quote
     *
     * @originalFile MailerController.php
     */
    public function quote(Request $request, int $quote_id)
    {
        if (! $this->mailer_configured) {
            return;
        }
        $email_template_id = get_setting('email_quote_template');
        $email_template    = '{}';
        if ($email_template_id) {
            $email_template = json_encode((new EmailTemplatesService())->getById($email_template_id));
        }
        $custom_fields = [];
        foreach (array_keys((new CustomFieldsService())->customTables()) as $table) {
            $custom_fields[$table] = (new CustomFieldsService())->byTable($table)->get()->result();
        }
        return view('mailer.quote', [
            'selected_email_template' => $email_template_id,
            'selected_pdf_template' => get_setting('pdf_quote_template'),
            'email_templates' => (new EmailTemplatesService())->where('email_template_type', 'quote')->get()->result(),
            'email_template' => $email_template,
            'custom_fields' => $custom_fields,
            'pdf_templates' => (new TemplatesService())->getQuoteTemplates(),
            'quote' => (new QuotesService())->getById($quote_id),
        ]);
    }

    /**
     * @originalName sendInvoice
     *
     * @originalFile MailerController.php
     */
    public function sendInvoice(Request $request, string $invoice_id)
    {
        if ($request->has('btn_cancel')) {
            return Redirect::to('invoices/view/' . $invoice_id);
        }
        if (! $this->mailer_configured) {
            return abort(response()->view('mailer.not_configured'), 503);
        }
        $to           = $request->input('to_email');
        $from         = [$request->input('from_email'), $request->input('from_name')];
        $pdf_template = $request->input('pdf_template');
        $subject      = $request->input('subject');
        $body         = $request->input('body');
        if (mb_strlen($body) != mb_strlen(strip_tags($body))) {
            $body = htmlspecialchars_decode($body, ENT_COMPAT);
        } else {
            $body = htmlspecialchars_decode(nl2br($body), ENT_COMPAT);
        }
        $cc  = $request->input('cc');
        $bcc = $request->input('bcc');
        $attachment_files = (new UploadsService())->getInvoiceUploads($invoice_id);
        (new InvoicesService())->generateInvoiceNumberIfApplicable($invoice_id);
        if (email_invoice($invoice_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files)) {
            (new InvoicesService())->markSent($invoice_id);
            Session::flash('alert_success', trans('email_successfully_sent'));
            return Redirect::to('invoices/view/' . $invoice_id);
        }
        return Redirect::to('mailer/invoice/' . $invoice_id);
    }

    /**
     * @originalName sendQuote
     *
     * @originalFile MailerController.php
     */
    public function sendQuote(Request $request, string $quote_id)
    {
        if ($request->has('btn_cancel')) {
            return Redirect::to('quotes/view/' . $quote_id);
        }
        if (! $this->mailer_configured) {
            return abort(response()->view('mailer.not_configured'), 503);
        }
        $to           = $request->input('to_email');
        $from         = [$request->input('from_email'), $request->input('from_name')];
        $pdf_template = $request->input('pdf_template');
        $subject      = $request->input('subject');
        $body         = $request->input('body');
        if (mb_strlen($body) != mb_strlen(strip_tags($body))) {
            $body = htmlspecialchars_decode($body, ENT_COMPAT);
        } else {
            $body = htmlspecialchars_decode(nl2br($body), ENT_COMPAT);
        }
        $cc  = $request->input('cc');
        $bcc = $request->input('bcc');
        $attachment_files = (new UploadsService())->getQuoteUploads($quote_id);
        (new QuotesService())->generateQuoteNumberIfApplicable($quote_id);
        if (email_quote($quote_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files)) {
            (new QuotesService())->markSent($quote_id);
            Session::flash('alert_success', trans('email_successfully_sent'));
            return Redirect::to('quotes/view/' . $quote_id);
        }
        return Redirect::to('mailer/quote/' . $quote_id);
    }
}
