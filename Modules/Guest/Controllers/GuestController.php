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
        $this->load->model(['quotes/mdl_quotes', 'invoices/mdl_invoices']);
        $this->layout->set(['overdue_invoices' => $this->mdl_invoices->isOverdue()->where_in('ip_invoices.client_id', $this->user_clients)->get()->result(), 'open_quotes' => $this->mdl_quotes->isOpen()->where_in('ip_quotes.client_id', $this->user_clients)->get()->result(), 'open_invoices' => $this->mdl_invoices->isOpen()->where_in('ip_invoices.client_id', $this->user_clients)->get()->result(), 'enable_online_payments' => get_setting('enable_online_payments')]);
        $this->layout->buffer('content', 'guest/index');
        $this->layout->render('layout_guest');
    }
}
