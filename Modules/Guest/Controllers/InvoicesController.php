<?php

namespace Modules\Guest\Controllers;

use Illuminate\Support\Facades\Log;

use AllowDynamicProperties;
use Modules\Core\Controllers\GuestController as BaseGuestController;

#[AllowDynamicProperties]
class InvoicesController extends BaseGuestController
{
    /**
     * InvoicesController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoices/mdl_invoices');
    }

    /**
     * @originalName index
     *
     * @originalFile InvoicesController.php
     */
    public function index(): void
    {
        // Display open invoices by default
        redirect()->route('guest/invoices/status/open');
    }

    /**
     * @originalName status
     *
     * @originalFile InvoicesController.php
     */
    public function status(string $status = 'open', $page = 0): void
    {
        // Determine which group of invoices to load
        switch ($status) {
            case 'all':
                (new InvoicesService())->guestVisible();
                break;
            case 'paid':
                (new InvoicesService())->isPaid();
                break;
            case 'overdue':
                (new InvoicesService())->isOverdue();
                break;
            default:
                (new InvoicesService())->isOpen();
                break;
        }
        (new InvoicesService())->where_in('ip_invoices.client_id', $this->user_clients);
        (new InvoicesService())->paginate(site_url('guest/invoices/status/' . $status), $page);
        $invoices = (new InvoicesService())->result();
        $this->layout->set(['invoices' => $invoices, 'status' => $status, 'enable_online_payments' => get_setting('enable_online_payments')]);
        $this->layout->buffer('content', 'guest/invoices_index');
        $this->layout->render('layout_guest');
    }

    /**
     * @originalName view
     *
     * @originalFile InvoicesController.php
     */
    public function view($invoice_id): void
    {
        $invoice = (new InvoicesService())->where('ip_invoices.invoice_id', $invoice_id)->where_in('ip_invoices.client_id', $this->user_clients)->get()->row();
        if ( ! $invoice) {
            show_404();
        }
        (new InvoicesService())->markViewed($invoice->invoice_id);
        $this->load->model(['invoices/mdl_items', 'invoices/mdl_invoice_tax_rates', 'upload/mdl_uploads']);
        $this->load->helper('dropzone');
        $this->layout->set(['invoice_id' => $invoice_id, 'invoice' => $invoice, 'items' => (new ItemsService())->where('invoice_id', $invoice_id)->get()->result(), 'invoice_tax_rates' => (new InvoiceTaxRatesService())->where('invoice_id', $invoice_id)->get()->result(), 'enable_online_payments' => get_setting('enable_online_payments'), 'legacy_calculation' => config_item('legacy_calculation')]);
        $this->layout->buffer('content', 'guest/invoices_view');
        $this->layout->render('layout_guest');
    }

    /**
     * @originalName generatePdf
     *
     * @originalFile InvoicesController.php
     */
    public function generatePdf($invoice_id, $stream = true, $invoice_template = null): void
    {
        $invoice = (new InvoicesService())->guestVisible()->where('ip_invoices.invoice_id', $invoice_id)->where_in('ip_invoices.client_id', $this->user_clients)->get()->row();
        if ( ! $invoice) {
            show_404();
        }
        (new InvoicesService())->markViewed($invoice_id);
        $this->load->helper('pdf');
        generate_invoice_pdf($invoice_id, $stream, $invoice_template, true);
    }

    /**
     * @originalName generateSumexPdf
     *
     * @originalFile InvoicesController.php
     */
    public function generateSumexPdf($invoice_id, $stream = true, $invoice_template = null): void
    {
        $invoice = (new InvoicesService())->guestVisible()->where('ip_invoices.invoice_id', $invoice_id)->where_in('ip_invoices.client_id', $this->user_clients)->get()->row();
        if ( ! $invoice) {
            show_404();
        }
        (new InvoicesService())->markViewed($invoice_id);
        $this->load->helper('pdf');
        generate_invoice_sumex($invoice_id, $stream, $invoice_template, true);
    }
}
