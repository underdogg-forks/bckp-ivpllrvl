<?php

namespace Modules\PaymentMethods\Controllers;

use Illuminate\Http\Request;
use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\PaymentMethods\Services\PaymentMethodsService;

#[AllowDynamicProperties]
class PaymentMethodsController extends AdminController
{
    /**
     * Initialize the PaymentMethodsController.
     *
     * Ensures base AdminController initialization is executed.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @originalName index
     *
     * @originalFile PaymentMethodsController.php
     */
    public function index($page = 0)
    {
        (new PaymentMethodsService())->paginate(site_url('payment_methods/index'), $page);
        $payment_methods = (new PaymentMethodsService())->result();
        $this->layout->set('payment_methods', $payment_methods);
        $this->layout->buffer('content', 'payment_methods/index');
        $this->layout->render();
    }

    /**
     * @originalName form
     *
     * @originalFile PaymentMethodsController.php
     */
    public function form(Request $request, $id = null) {
        if ($request->post('btn_cancel')) {
            redirect()->route('payment_methods');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ($request->post('is_update') == 0 && $request->post('payment_method_name') != '') {
            $check = $this->db->get_where('ip_payment_methods', ['payment_method_name' => $request->post('payment_method_name')])->result();
            if ( ! empty($check)) {
                $this->session->set_flashdata('alert_error', trans('payment_method_already_exists'));
                redirect()->route('payment_methods/form');
            }
        }
        if ((new PaymentMethodsService())->runValidation()) {
            (new PaymentMethodsService())->save($id);
            redirect()->route('payment_methods');
        }
        if ($id && ! $request->post('btn_submit')) {
            if ( ! (new PaymentMethodsService())->prepForm($id)) {
                show_404();
            }
            (new PaymentMethodsService())->setFormValue('is_update', true);
        }
        $this->layout->buffer('content', 'payment_methods/form');
        $this->layout->render();
    }

    /**
     * Delete a payment method and redirect to the payment methods index.
     *
     * Deletes the payment method identified by the given ID, then redirects to the
     * payment methods listing route.
     *
     * @param int|string $id the identifier of the payment method to delete
     */
    public function delete($id)
    {
        (new PaymentMethodsService())->delete($id);
        redirect()->route('payment_methods');
    }
}
