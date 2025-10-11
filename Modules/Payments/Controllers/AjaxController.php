<?php

namespace Modules\Payments\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\PaymentMethods\Services\PaymentMethodsService;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    public $ajax_controller = true;

    /**
     * Handle an AJAX request to add a payment.
     *
     * Processes input validation and attempts to persist a new payment, then outputs a JSON response.
     * On success the JSON contains `success` = 1 and `payment_id` with the new payment's ID.
     * On validation failure the JSON contains `success` = 0 and `validation_errors` with the validation error payload.
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
     * Displays the "Add Payment" modal and supplies it with required payment data.
     *
     * Prepares an array passed to the 'payments/modal_add_payment' view containing:
     * - `payment_methods`: list of available payment methods,
     * - `invoice_id`: sanitized invoice identifier,
     * - `invoice_balance`: invoice balance from POST,
     * - `invoice_payment_method`: selected payment method from POST,
     * - `payment_cf_exist`: sanitized flag indicating whether payment custom fields exist.
     */
    public function modalAddPayment()
    {
        $this->load->module('layout');
        $this->load->model('payments/mdl_payments');
        $this->load->model('payment_methods/mdl_payment_methods');
        $this->load->model('custom_fields/mdl_payment_custom');
        $data = ['payment_methods' => (new PaymentMethodsService())->getAll(), 'invoice_id' => $this->security->xss_clean($this->input->post('invoice_id')), 'invoice_balance' => $this->input->post('invoice_balance'), 'invoice_payment_method' => $this->input->post('invoice_payment_method'), 'payment_cf_exist' => $this->security->xss_clean($this->input->post('payment_cf_exist'))];
        $this->layout->loadView('payments/modal_add_payment', $data);
    }
}