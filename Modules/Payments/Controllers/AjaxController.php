<?php

namespace Modules\Payments\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\PaymentMethods\Models\PaymentMethod;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    public $ajax_controller = true;

    /**
     * @originalName add
     *
     * @originalFile AjaxController.php
     */
    public function add()
    {
        $this->load->model('payments/mdl_payments');
        if ((new PaymentsService())->runValidation()) {
            $payment_id = (new PaymentsService())->save();
            $response   = ['success' => 1, 'payment_id' => $payment_id];
        } else {
            $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        echo json_encode($response);
    }

    /**
     * @originalName modalAddPayment
     *
     * @originalFile AjaxController.php
     */
    public function modalAddPayment()
    {
        $this->load->module('layout');
        $this->load->model('payments/mdl_payments');
        $this->load->model('payment_methods/mdl_payment_methods');
        $this->load->model('custom_fields/mdl_payment_custom');
        $data = ['payment_methods' => PaymentMethod::query()->get(), 'invoice_id' => $this->security->xss_clean($this->input->post('invoice_id')), 'invoice_balance' => $this->input->post('invoice_balance'), 'invoice_payment_method' => $this->input->post('invoice_payment_method'), 'payment_cf_exist' => $this->security->xss_clean($this->input->post('payment_cf_exist'))];
        $this->layout->loadView('payments/modal_add_payment', $data);
    }
}
