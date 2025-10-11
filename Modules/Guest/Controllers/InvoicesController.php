<?php

namespace Modules\Guest\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\GuestController as BaseGuestController;
use Modules\Invoices\Services\InvoiceTaxRatesService;
use Modules\Invoices\Services\ItemsService;

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
     * Display a paginated list of guest-visible invoices filtered by status.
     *
     * Loads invoices for the current user's clients filtered by $status ('all', 'paid', 'overdue', or 'open'),
     * paginates the results, and prepares data for the guest invoices index view.
     *
     * @param string $status The invoice group to display: 'all', 'paid', 'overdue', or 'open' (default).
     * @param int $page Pagination page index.
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
     * Load and display a single invoice for the current guest and render the guest layout.
     *
     * Loads the invoice restricted to the guest's associated clients, marks it as viewed, prepares related view data
     * (items, invoice tax rates, uploads and relevant settings), buffers the invoice view, and renders the guest layout.
     * Shows a 404 response if the invoice cannot be found.
     *
     * @param int|string $invoice_id The invoice identifier to load and display.
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
        $this->layout->set(['invoice_id' => $invoice_id, 'invoice' => $invoice, 'items' => (new ItemsService())->getByInvoiceId($invoice_id), 'invoice_tax_rates' => (new InvoiceTaxRatesService())->getByInvoiceId($invoice_id), 'enable_online_payments' => get_setting('enable_online_payments'), 'legacy_calculation' => config_item('legacy_calculation')]);
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
     * Generate a Sumex-format PDF for a guest-visible invoice.
     *
     * If the invoice is not accessible to the current guest, a 404 response is shown.
     * The invoice is marked as viewed before PDF generation. When `$stream` is true the
     * generated PDF is streamed to the client; when false the PDF is produced but not streamed.
     *
     * @param int|string $invoice_id The invoice identifier.
     * @param bool $stream Whether to stream the PDF to the client (`true`) or not (`false`).
     * @param string|null $invoice_template Optional invoice template identifier to use.
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