<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Guest\Controllers;

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */
#[AllowDynamicProperties]
class PaymentsController extends GuestController
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
