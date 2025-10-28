<?php

namespace Modules\Core\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\GuestController as BaseGuestController;
use Modules\Guest\Controllers\QuotesService;
use Modules\Quotes\Services\QuoteItemsService;
use Modules\Quotes\Services\QuoteTaxRatesService;
use function Modules\Guest\Controllers\config_item;
use function Modules\Guest\Controllers\show_404;
use function Modules\Guest\Controllers\site_url;

#[AllowDynamicProperties]
class QuotesController extends BaseGuestController
{
    /**
     * Initialize the guest quotes controller.
     *
     * Sets up controller state by invoking the base guest controller constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @originalName index
     *
     * @originalFile QuotesController.php
     */
    public function index()
    {
        // Display open quotes by default
        redirect()->route('guest/quotes/status/open');
    }

    /**
     * Display a paginated list of guest-visible quotes filtered by status.
     *
     * Applies a guest-visible scope, filters quotes according to the provided
     * status ('all', 'viewed', 'approved', 'rejected', or 'open' by default),
     * restricts results to the current guest's associated clients, and paginates
     * the results. Prepares layout data with the retrieved quotes and active
     * status, enables the invoice column when status is 'rejected', and renders
     * the guest quotes index within the guest layout.
     *
     * @param string $status the filter to apply: 'all', 'viewed', 'approved', 'rejected', or 'open'
     * @param int    $page   the pagination page number to display
     *
     * @return void
     */
    public function status(string $status = 'open', $page = 0)
    {
        redirect_to_set();
        // Sets the current URL in the session to force redirect_to()
        // Determine which group of quotes to load
        switch ($status) {
            case 'all':
                (new QuotesService())->guestVisible();
                break;
            case 'viewed':
                (new QuotesService())->isViewed();
                break;
            case 'approved':
                (new QuotesService())->isApproved();
                break;
            case 'rejected':
                (new QuotesService())->isRejected();
                $this->layout->set('show_invoice_column', true);
                break;
            default:
                (new QuotesService())->isOpen();
                break;
        }
        (new QuotesService())->where_in('ip_quotes.client_id', $this->user_clients);
        (new QuotesService())->paginate(site_url('guest/quotes/status/' . $status), $page);
        $quotes = (new QuotesService())->result();
        $this->layout->set(['quotes' => $quotes, 'status' => $status]);
        $this->layout->buffer('content', 'guest/quotes_index');
        $this->layout->render('layout_guest');
    }

    /**
     * Display a single quote to the current guest client and render the guest layout.
     *
     * Marks the quote as viewed and makes the quote, its items, and its tax rates available to the view.
     * If the quote is not accessible to the current guest, a 404 response is shown.
     *
     * @param int|string $quote_id the identifier of the quote to display
     */
    public function view($quote_id)
    {
        redirect_to_set();
        // Sets the current URL in the session to force redirect_to()
        $quote = (new QuotesService())->guestVisible()->where('ip_quotes.quote_id', $quote_id)->where_in('ip_quotes.client_id', $this->user_clients)->get()->row();
        if ( ! $quote) {
            show_404();
        }
        (new QuotesService())->markViewed($quote->quote_id);
        $this->load->helper('dropzone');
        $this->layout->set(['quote_id' => $quote_id, 'quote' => $quote, 'items' => (new QuoteItemsService())->getByQuoteId($quote_id), 'quote_tax_rates' => (new QuoteTaxRatesService())->getByQuoteId($quote_id), 'legacy_calculation' => config_item('legacy_calculation')]);
        $this->layout->buffer('content', 'guest/quotes_view');
        $this->layout->render('layout_guest');
    }

    /**
     * Generate and send a PDF for a guest-visible quote.
     *
     * Marks the quote as viewed. If the quote is not accessible to the current guest, sends a 404 response.
     * Otherwise generates the quote PDF and either streams it to the client or produces it without streaming.
     *
     * @param int|string  $quote_id       the quote identifier
     * @param bool        $stream         true to stream the PDF to the client, false to generate without streaming
     * @param string|null $quote_template optional template identifier to use when generating the PDF
     */
    public function generatePdf($quote_id, $stream = true, $quote_template = null)
    {
        $this->load->helper('pdf');
        (new QuotesService())->markViewed($quote_id);
        $quote = (new QuotesService())->guestVisible()->where('ip_quotes.quote_id', $quote_id)->where_in('ip_quotes.client_id', $this->user_clients)->get()->row();
        if ( ! $quote) {
            show_404();
        }
        generate_quote_pdf($quote_id, $stream, $quote_template);
    }

    /**
     * Approve a quote, notify its recipients of the approval, and redirect to the guest quotes list.
     *
     * @param string $quote_id the quote identifier to approve
     */
    public function approve(string $quote_id)
    {
        (new QuotesService())->approveQuoteById($quote_id);
        email_quote_status($quote_id, 'approved');
        redirect_to('guest/quotes');
    }

    /**
     * Mark a quote as rejected, notify recipients of the rejection, and redirect to the guest quotes listing.
     *
     * @param string $quote_id the quote's unique identifier
     */
    public function reject(string $quote_id)
    {
        (new QuotesService())->rejectQuoteById($quote_id);
        email_quote_status($quote_id, 'rejected');
        redirect_to('guest/quotes');
    }
}
