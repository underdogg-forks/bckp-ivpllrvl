<?php

namespace Modules\Guest\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\GuestController as BaseGuestController;

#[AllowDynamicProperties]
class QuotesController extends BaseGuestController
{
    /**
     * QuotesController constructor.
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
     * @originalName status
     *
     * @originalFile QuotesController.php
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
     * @originalName view
     *
     * @originalFile QuotesController.php
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
        $this->layout->set(['quote_id' => $quote_id, 'quote' => $quote, 'items' => (new QuoteItemsService())->where('quote_id', $quote_id)->get()->result(), 'quote_tax_rates' => (new QuoteTaxRatesService())->where('quote_id', $quote_id)->get()->result(), 'legacy_calculation' => config_item('legacy_calculation')]);
        $this->layout->buffer('content', 'guest/quotes_view');
        $this->layout->render('layout_guest');
    }

    /**
     * @originalName generatePdf
     *
     * @originalFile QuotesController.php
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
     * Approve the specified quote, notify recipients of the approval, and redirect to the guest quotes list.
     *
     * Approves the quote identified by $quote_id, triggers the quote-status email for "approved", and then redirects the user to the guest quotes index.
     *
     * @param string $quote_id The identifier of the quote to approve.
     */
    public function approve(string $quote_id)
    {
        (new QuotesService())->approveQuoteById($quote_id);
        email_quote_status($quote_id, 'approved');
        redirect_to('guest/quotes');
    }

    /**
     * Rejects the specified quote, sends a rejection notification, and redirects to the guest quotes listing.
     *
     * Rejects the quote identified by `$quote_id`, triggers an email notifying relevant parties of the rejection, and redirects the user to the guest quotes index.
     *
     * @param string $quote_id The identifier of the quote to reject.
     */
    public function reject(string $quote_id)
    {
        (new QuotesService())->rejectQuoteById($quote_id);
        email_quote_status($quote_id, 'rejected');
        redirect_to('guest/quotes');
    }
}