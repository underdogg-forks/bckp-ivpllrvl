<?php

namespace Modules\Payments\Controllers;

use Modules\Core\Controllers\AdminController;

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */
#[AllowDynamicProperties]
class PaymentsController extends AdminController
{
    /**
     * PaymentsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_payments');
    }
    /**
     * @originalName index
     *
     * @originalFile PaymentsController.php
     */
    public function index($page = 0)
    {
        $this->mdl_payments->paginate(site_url('payments/index'), $page);
        $payments = $this->mdl_payments->result();
        $this->layout->set(['filter_display' => true, 'filter_placeholder' => trans('filter_payments'), 'filter_method' => 'filter_payments', 'payments' => $payments]);
        $this->layout->buffer('content', 'payments/index');
        $this->layout->render();
    }
    /**
     * @originalName form
     *
     * @originalFile PaymentsController.php
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('payments');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        $this->load->model('custom_fields/mdl_payment_custom');
        if ($this->mdl_payments->runValidation()) {
            $id = $this->mdl_payments->save($id);
            $this->mdl_payment_custom->saveCustom($id, $this->input->post('custom'));
            redirect('payments');
        }
        if (!$this->input->post('btn_submit')) {
            $prep_form = $this->mdl_payments->prepForm($id);
            if ($id && !$prep_form) {
                show_404();
            }
            $this->load->model('custom_values/mdl_custom_values');
            $payment_custom = $this->mdl_payment_custom->where('payment_id', $id)->get();
            if ($payment_custom->numRows()) {
                $payment_custom = $payment_custom->row();
                unset($payment_custom->payment_id, $payment_custom->payment_custom_id);
                foreach ($payment_custom as $key => $val) {
                    $this->mdl_payments->setFormValue('custom[' . $key . ']', $val);
                }
            }
        } elseif ($this->input->post('custom')) {
            foreach ($this->input->post('custom') as $key => $val) {
                $this->mdl_payments->setFormValue('custom[' . $key . ']', $val);
            }
        }
        $this->load->helper('custom_values');
        $this->load->model(['invoices/mdl_invoices', 'payment_methods/mdl_payment_methods', 'custom_fields/mdl_custom_fields', 'custom_values/mdl_custom_values']);
        $open_invoices = $this->mdl_invoices->isOpen()->get()->result();
        $custom_fields = $this->mdl_custom_fields->byTable('ip_payment_custom')->get()->result();
        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, $this->mdl_custom_values->customValueFields())) {
                $values = $this->mdl_custom_values->getByFid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }
        $fields = $this->mdl_payment_custom->getByPayid($id);
        foreach ($custom_fields as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->payment_custom_fieldid == $cfield->custom_field_id) {
                    // TODO: Hackish, may need a better optimization
                    $this->mdl_payments->setFormValue('custom[' . $cfield->custom_field_id . ']', $fvalue->payment_custom_fieldvalue);
                    break;
                }
            }
        }
        $amounts = [];
        $invoice_payment_methods = [];
        foreach ($open_invoices as $open_invoice) {
            $amounts['invoice' . $open_invoice->invoice_id] = format_amount($open_invoice->invoice_balance);
            $invoice_payment_methods['invoice' . $open_invoice->invoice_id] = $open_invoice->payment_method;
        }
        $this->layout->set(['payment_id' => $id, 'payment_methods' => $this->mdl_payment_methods->get()->result(), 'open_invoices' => $open_invoices, 'custom_fields' => $custom_fields, 'custom_values' => $custom_values, 'amounts' => json_encode($amounts), 'invoice_payment_methods' => json_encode($invoice_payment_methods)]);
        if ($id) {
            $this->layout->set('payment', $this->mdl_payments->where('ip_payments.payment_id', $id)->get()->row());
        }
        $this->layout->buffer('content', 'payments/form');
        $this->layout->render();
    }
    /**
     * @originalName onlineLogs
     *
     * @originalFile PaymentsController.php
     */
    public function onlineLogs($page = 0)
    {
        $this->load->model('payments/mdl_payment_logs');
        $this->mdl_payment_logs->paginate(site_url('payments/online_logs'), $page);
        $payment_logs = $this->mdl_payment_logs->result();
        $this->layout->set(['filter_display' => true, 'filter_placeholder' => trans('filter_online_logs'), 'filter_method' => 'filter_online_logs', 'payment_logs' => $payment_logs]);
        $this->layout->buffer('content', 'payments/online_logs');
        $this->layout->render();
    }
    /**
     * @originalName delete
     *
     * @originalFile PaymentsController.php
     */
    public function delete($id)
    {
        $this->mdl_payments->delete($id);
        redirect('payments');
    }
}
