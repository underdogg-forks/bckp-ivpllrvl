<?php

namespace Modules\Guest\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\GuestController as BaseGuestController;

#[AllowDynamicProperties]
class GuestController extends BaseGuestController
{
    /**
     * Prepare guest dashboard data and render the guest layout.
     *
     * Prepares view data including `overdue_invoices`, `open_quotes`, `open_invoices`,
     * and `enable_online_payments`, buffers the guest index content, and renders the
     * guest layout.
     *
     * @return void
     */
    public function index()
    {
        $this->layout->set(['overdue_invoices' => (new InvoicesService())->isOverdue()->where_in('ip_invoices.client_id', $this->user_clients)->get()->result(), 'open_quotes' => (new QuotesService())->isOpen()->where_in('ip_quotes.client_id', $this->user_clients)->get()->result(), 'open_invoices' => (new InvoicesService())->isOpen()->where_in('ip_invoices.client_id', $this->user_clients)->get()->result(), 'enable_online_payments' => get_setting('enable_online_payments')]);
        $this->layout->buffer('content', 'guest/index');
        $this->layout->render('layout_guest');
    }
}