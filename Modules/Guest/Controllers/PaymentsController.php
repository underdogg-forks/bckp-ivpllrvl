<?php

namespace Modules\Guest\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\GuestController as BaseGuestController;
use Modules\Payments\Services\PaymentsService;

#[AllowDynamicProperties]
class PaymentsController extends BaseGuestController
{
    /**
     * Initialize the PaymentsController.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @originalName index
     *
     * @originalFile PaymentsController.php
     */
    public function index($page = 0)
    {
        (new PaymentsService())->where('(ip_payments.invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE client_id IN (' . implode(',', $this->user_clients) . ')))');
        (new PaymentsService())->paginate(site_url('guest/payments/index'), $page);
        $payments = (new PaymentsService())->result();
        $this->layout->set(['payments' => $payments, 'filter_display' => true, 'filter_placeholder' => trans('filter_payments'), 'filter_method' => 'filter_payments']);
        $this->layout->buffer('content', 'guest/payments_index');
        $this->layout->render('layout_guest');
    }
}