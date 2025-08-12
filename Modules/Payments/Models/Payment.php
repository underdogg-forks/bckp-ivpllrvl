<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Payments\Models;

use Modules\Core\Models\ResponseModel;
/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */
#[AllowDynamicProperties]
class Payment extends ResponseModel
{
    public $table = 'ip_payments';
    public $primary_key = 'ip_payments.payment_id';
    public $validation_rules = 'validation_rules';
    /**
     * @originalName defaultSelect
     *
     * @originalFile Payment.php
     */
    public function defaultSelect()
    {
        $this->db->select('
            SQL_CALC_FOUND_ROWS
            ip_payment_methods.*,
            ip_invoice_amounts.*,
            ip_clients.client_name,
            ip_clients.client_surname,
            ip_clients.client_title,
            ip_clients.client_id,
            ip_invoices.invoice_number,
            ip_invoices.invoice_date_created,
            ip_payments.*', false);
    }
    /**
     * @originalName defaultOrderBy
     *
     * @originalFile Payment.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('ip_payments.payment_date DESC, ip_payments.payment_id DESC');
    }
    /**
     * @originalName defaultJoin
     *
     * @originalFile Payment.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_invoices', 'ip_invoices.invoice_id = ip_payments.invoice_id');
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_invoices.client_id');
        $this->db->join('ip_invoice_amounts', 'ip_invoice_amounts.invoice_id = ip_invoices.invoice_id');
        $this->db->join('ip_payment_methods', 'ip_payment_methods.payment_method_id = ip_payments.payment_method_id', 'left');
    }
    /**
     * @originalName validationRules
     *
     * @originalFile Payment.php
     */
    public function validationRules()
    {
        return ['invoice_id' => ['field' => 'invoice_id', 'label' => trans('invoice'), 'rules' => 'required'], 'payment_date' => ['field' => 'payment_date', 'label' => trans('date'), 'rules' => 'required'], 'payment_amount' => ['field' => 'payment_amount', 'label' => trans('payment'), 'rules' => 'required|callback_validate_payment_amount'], 'payment_method_id' => ['field' => 'payment_method_id', 'label' => trans('payment_method')], 'payment_note' => ['field' => 'payment_note', 'label' => trans('note')]];
    }
    /**
     * @originalName validatePaymentAmount
     *
     * @originalFile Payment.php
     */
    public function validatePaymentAmount($amount)
    {
        $amount = (float) standardize_amount($amount);
        $invoice_id = $this->input->post('invoice_id');
        $payment_id = $this->input->post('payment_id');
        $invoice = $this->db->where('invoice_id', $invoice_id)->get('ip_invoice_amounts')->row();
        if ($invoice == null) {
            return false;
        }
        $invoice_balance = (float) $invoice->invoice_balance;
        if ($payment_id) {
            $payment = $this->db->where('payment_id', $payment_id)->get('ip_payments')->row();
            $invoice_balance += (float) $payment->payment_amount;
        }
        if ($amount > $invoice_balance) {
            $this->form_validation->set_message('validate_payment_amount', trans('payment_cannot_exceed_balance'));
            return false;
        }
        return true;
    }
    /**
     * @originalName save
     *
     * @originalFile Payment.php
     */
    public function save($id = null, $db_array = null)
    {
        $db_array = $db_array ? $db_array : $this->dbArray();
        $this->load->model('invoices/mdl_invoice_amounts');
        // Save the payment
        $id = parent::save($id, $db_array);
        $global_discount['item'] = $this->mdl_invoice_amounts->getGlobalDiscount($db_array['invoice_id']);
        // Recalculate invoice amounts
        $this->mdl_invoice_amounts->calculate($db_array['invoice_id'], $global_discount);
        // Set proper status for the invoice
        $invoice = $this->db->where('invoice_id', $db_array['invoice_id'])->get('ip_invoice_amounts')->row();
        if ($invoice == null) {
            return false;
        }
        // Calculate sum for payments
        $paid = (float) $invoice->invoice_paid;
        $total = (float) $invoice->invoice_total;
        if ($paid >= $total) {
            $this->db->where('invoice_id', $db_array['invoice_id']);
            $this->db->set('invoice_status_id', 4);
            $this->db->update('ip_invoices');
        }
        $global_discount['item'] = $this->mdl_invoice_amounts->getGlobalDiscount($db_array['invoice_id']);
        // Recalculate invoice amounts
        $this->mdl_invoice_amounts->calculate($db_array['invoice_id'], $global_discount);
        return $id;
    }
    /**
     * @originalName dbArray
     *
     * @originalFile Payment.php
     */
    public function dbArray()
    {
        $db_array = parent::dbArray();
        $db_array['payment_date'] = date_to_mysql($db_array['payment_date']);
        $db_array['payment_amount'] = standardize_amount($db_array['payment_amount']);
        return $db_array;
    }
    /**
     * @originalName delete
     *
     * @originalFile Payment.php
     */
    public function delete($id = null)
    {
        // GetController the invoice id before deleting payment
        $this->db->select('invoice_id');
        $this->db->where('payment_id', $id);
        $invoice_id = $this->db->get('ip_payments')->row()->invoice_id;
        // Delete the payment
        parent::delete($id);
        $this->load->model('invoices/mdl_invoice_amounts');
        $global_discount['item'] = $this->mdl_invoice_amounts->getGlobalDiscount($invoice_id);
        // Recalculate invoice amounts
        $this->mdl_invoice_amounts->calculate($invoice_id, $global_discount);
        // Change invoice status back to sent
        $this->db->select('invoice_status_id');
        $this->db->where('invoice_id', $invoice_id);
        $invoice = $this->db->get('ip_invoices')->row();
        if ($invoice->invoice_status_id == 4) {
            $this->db->where('invoice_id', $invoice_id);
            $this->db->set('invoice_status_id', 2);
            $this->db->update('ip_invoices');
        }
        $this->load->helper('orphan');
        delete_orphans();
    }
    /**
     * @originalName prepForm
     *
     * @originalFile Payment.php
     */
    public function prepForm($id = null): bool
    {
        if (!parent::prepForm($id)) {
            return false;
        }
        if (!$id) {
            parent::setFormValue('payment_date', date('Y-m-d'));
        }
        return true;
    }
    /**
     * @originalName byClient
     *
     * @originalFile Payment.php
     */
    public function byClient($client_id)
    {
        $this->filter_where('ip_clients.client_id', $client_id);
        return $this;
    }
}
