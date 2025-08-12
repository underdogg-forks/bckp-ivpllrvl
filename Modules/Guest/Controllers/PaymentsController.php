<?php

namespace Modules\Guest\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\GuestController as BaseGuestController;

#[AllowDynamicProperties]
class PaymentsController extends BaseGuestController
{
    /**
     * PaymentsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('payments/mdl_payments');
    }

    /**
     * @originalName index
     *
     * @originalFile PaymentsController.php
     */
    public function index($page = 0)
    {
        $this->mdl_payments->where('(ip_payments.invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE client_id IN (' . implode(',', $this->user_clients) . ')))');
        $this->mdl_payments->paginate(site_url('guest/payments/index'), $page);
        $payments = $this->mdl_payments->result();
        $this->layout->set(['payments' => $payments, 'filter_display' => true, 'filter_placeholder' => trans('filter_payments'), 'filter_method' => 'filter_payments']);
        $this->layout->buffer('content', 'guest/payments_index');
        $this->layout->render('layout_guest');
    }
}
