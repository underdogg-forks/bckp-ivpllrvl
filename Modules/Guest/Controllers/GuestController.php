<?php

namespace Modules\Guest\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\GuestController as BaseGuestController;

#[AllowDynamicProperties]
class GuestController extends BaseGuestController
{
    /**
     * @originalName index
     *
     * @originalFile GuestController.php
     */
    public function index()
    {
        $this->layout->set(['overdue_invoices' => (new InvoicesService())->isOverdue()->where_in('ip_invoices.client_id', $this->user_clients)->get()->result(), 'open_quotes' => (new QuotesService())->isOpen()->where_in('ip_quotes.client_id', $this->user_clients)->get()->result(), 'open_invoices' => (new InvoicesService())->isOpen()->where_in('ip_invoices.client_id', $this->user_clients)->get()->result(), 'enable_online_payments' => get_setting('enable_online_payments')]);
        $this->layout->buffer('content', 'guest/index');
        $this->layout->render('layout_guest');
    }
}
