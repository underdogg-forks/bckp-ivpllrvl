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
        $this->mdl_payment_methods->paginate(site_url('payment_methods/index'), $page);
        $payment_methods = $this->mdl_payment_methods->result();
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
            redirect('payment_methods');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ($this->input->post('is_update') == 0 && $this->input->post('payment_method_name') != '') {
            $check = $this->db->get_where('ip_payment_methods', ['payment_method_name' => $this->input->post('payment_method_name')])->result();
            if ( ! empty($check)) {
                $this->session->set_flashdata('alert_error', trans('payment_method_already_exists'));
                redirect('payment_methods/form');
            }
        }
        if ($this->mdl_payment_methods->runValidation()) {
            $this->mdl_payment_methods->save($id);
            redirect('payment_methods');
        }
        if ($id && ! $this->input->post('btn_submit')) {
            if ( ! $this->mdl_payment_methods->prepForm($id)) {
                show_404();
            }
            $this->mdl_payment_methods->setFormValue('is_update', true);
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
        $this->mdl_payment_methods->delete($id);
        redirect('payment_methods');
    }
}
