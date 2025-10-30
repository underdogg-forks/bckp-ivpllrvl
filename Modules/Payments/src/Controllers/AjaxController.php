<?php

namespace Modules\Payments\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\Payments\app\Services\PaymentMethodsService;
use Modules\Payments\Services\PaymentsService;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    public $ajax_controller = true;

    /**
     * Handle an AJAX request to add a payment and emit a JSON response.
     *
     * On success the response is an array with `success` = 1 and `payment_id` set to the created payment's ID.
     * On validation failure the response is an array with `success` = 0 and `validation_errors` containing the validation error details.
     *
     * @return void
     */
    public function add()
    {
        if ((new PaymentsService())->runValidation()) {
            $payment_id = (new PaymentsService())->save();
            $response   = ['success' => 1, 'payment_id' => $payment_id];
        } else {
// TODO: Laravel autoloads helpers - $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        echo json_encode($response);
    }

    /**
     * Renders the "Add Payment" modal populated with payment methods and POSTed invoice data.
     *
     * Prepares the view data array with the following keys:
     * - `payment_methods`: list of available payment methods.
     * - `invoice_id`: sanitized POST value for invoice ID.
     * - `invoice_balance`: POST value for invoice balance.
     * - `invoice_payment_method`: POST value for the selected payment method.
     * - `payment_cf_exist`: sanitized POST value indicating custom fields existence.
     *
     * The method then loads the 'payments/modal_add_payment' view through the layout module.
     */
    public function modalAddPayment()
    {
// TODO: Modules handled differently in Laravel - $this->load->module('layout');
        $data = ['payment_methods' => (new PaymentMethodsService())->getAll(), 'invoice_id' => e(request()->input('invoice_id')), 'invoice_balance' => request()->input('invoice_balance'), 'invoice_payment_method' => request()->input('invoice_payment_method'), 'payment_cf_exist' => e(request()->input('payment_cf_exist'))];
        $this->layout->loadView('payments/modal_add_payment', $data);
    }
}
