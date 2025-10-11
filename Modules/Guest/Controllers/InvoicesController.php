<?php

namespace Modules\Guest\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\GuestController as BaseGuestController;
use Modules\Invoices\Services\InvoiceTaxRatesService;

#[AllowDynamicProperties]
class InvoicesController extends BaseGuestController
{
    /**
     * Initialize the InvoicesController for guest access.
     *
     * Sets up controller state by delegating to the BaseGuestController constructor.
     */
    public function __construct()
    {
        parent::__construct();
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
     * Display a paginated list of invoices visible to the guest, filtered by status.
     *
     * Loads invoices for the current guest's clients according to the given status,
     * paginates the results, and renders the guest invoices index layout.
     *
     * @param string $status The invoice status filter: 'open' (default), 'all', 'paid', or 'overdue'.
     * @param int $page The pagination page number (zero-based).
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
     * Display the invoice page for a guest user.
     *
     * Loads the invoice belonging to one of the guest's clients, returns a 404 if not found,
     * marks the invoice as viewed, prepares layout data (invoice, items, tax rates and payment settings),
     * and renders the guest invoice view.
     *
     * @param int|string $invoice_id The invoice identifier.
     */
    public function view($invoice_id): void
    {
        $invoice = (new InvoicesService())->where('ip_invoices.invoice_id', $invoice_id)->where_in('ip_invoices.client_id', $this->user_clients)->get()->row();
        if ( ! $invoice) {
            show_404();
        }
        (new InvoicesService())->markViewed($invoice->invoice_id);
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
     * Generate and deliver a Sumex-formatted PDF for a guest invoice.
     *
     * If the invoice does not belong to any of the guest's clients, a 404 page is shown.
     * The invoice is marked as viewed before the PDF is generated; the PDF is produced
     * and either streamed or returned according to the `$stream` flag.
     *
     * @param int|string $invoice_id Identifier of the invoice to generate the Sumex PDF for.
     * @param bool $stream If true, stream the PDF to the client; if false, return or save it according to the PDF helper's behavior.
     * @param string|null $invoice_template Optional invoice template to use when generating the PDF.
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