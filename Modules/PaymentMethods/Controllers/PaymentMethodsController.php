<?php

namespace Modules\PaymentMethods\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class PaymentMethodsController extends AdminController
{
    /**
     * Payment_Methods constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_payment_methods');
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
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect()->route('payment_methods');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ($this->input->post('is_update') == 0 && $this->input->post('payment_method_name') != '') {
            $check = $this->db->get_where('ip_payment_methods', ['payment_method_name' => $this->input->post('payment_method_name')])->result();
            if ( ! empty($check)) {
                $this->session->set_flashdata('alert_error', trans('payment_method_already_exists'));
                redirect()->route('payment_methods/form');
            }
        }
        if ((new PaymentMethodsService())->runValidation()) {
            (new PaymentMethodsService())->save($id);
            redirect()->route('payment_methods');
        }
        if ($id && ! $this->input->post('btn_submit')) {
            if ( ! (new PaymentMethodsService())->prepForm($id)) {
                show_404();
            }
            (new PaymentMethodsService())->setFormValue('is_update', true);
        }
        $this->layout->buffer('content', 'payment_methods/form');
        $this->layout->render();
    }

    /**
     * @originalName delete
     *
     * @originalFile PaymentMethodsController.php
     */
    public function delete($id)
    {
        (new PaymentMethodsService())->delete($id);
        redirect()->route('payment_methods');
    }
}
