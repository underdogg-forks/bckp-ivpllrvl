<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Guest\Controllers\Gateways;

use Modules\Core\Controllers\BaseController;
/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */
#[AllowDynamicProperties]
class PaypalController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->createClient();
    }
    /**
     * @originalName paypalCreateOrder
     *
     * @originalFile PaypalController.php
     */
    public function paypalCreateOrder($invoice_url_key)
    {
        // Check if the invoice exists and is billable
        $this->load->model('invoices/mdl_invoices');
        $invoice = $this->mdl_invoices->where('ip_invoices.invoice_url_key', $invoice_url_key)->get()->row();
        // Check if the invoice is payable
        if ($invoice->invoice_balance <= 0) {
            $this->session->set_userdata('alert_error', lang('invoice_already_paid'));
            redirect(site_url('guest/view/invoice/' . $invoice->invoice_url_key));
        }
        //create the order
        $paypal_client = $this->lib_paypal->createOrder(['invoice_id' => $invoice->invoice_id, 'currency_code' => get_setting('gateway_paypal_currency'), 'value' => $invoice->invoice_balance, 'custom_id' => $invoice_url_key]);
        return $this->output->set_output($paypal_client);
        //TODO: make proper response
    }
    /**
     * @originalName paypalCapturePayment
     *
     * @originalFile PaypalController.php
     */
    public function paypalCapturePayment(string $order_id)
    {
        $paypal_response = $this->lib_paypal->captureOrder($order_id);
        //handle the payment
        if ($paypal_response['status']) {
            $paypal_object = json_decode($paypal_response['response']->getBody());
            $invoice_id = $paypal_object->purchase_units[0]->payments->captures[0]->invoice_id;
            $amount = $paypal_object->purchase_units[0]->payments->captures[0]->amount->value;
            //record the payment
            $this->load->model('payments/mdl_payments');
            $this->mdl_payments->save(null, ['invoice_id' => $invoice_id, 'payment_date' => date('Y-m-d'), 'payment_amount' => $amount, 'payment_method_id' => get_setting('gateway_paypal_payment_method'), 'payment_note' => '']);
            $invoice = $this->mdl_invoices->where('ip_invoices.invoice_id', $invoice_id)->get()->row();
            $this->session->set_flashdata('alert_success', sprintf(trans('online_payment_payment_successful'), $invoice->invoice_number));
            $this->session->keep_flashdata('alert_success');
            $this->db->insert('ip_merchant_responses', ['invoice_id' => $invoice_id, 'merchant_response_successful' => true, 'merchant_response_date' => date('Y-m-d'), 'merchant_response_driver' => 'paypal', 'merchant_response' => $paypal_object->status, 'merchant_response_reference' => 'Resource ID:' . $paypal_object->id]);
        } else {
            $response_error = json_decode($paypal_response['error']->getResponse()->getBody());
            //get the order details to have the invoice id from paypal
            $order_details = json_decode($this->paypal->showOrderDetails($order_id));
            //record the failed transaction in the logs
            $this->db->insert('ip_merchant_responses', ['invoice_id' => $order_details->purchase_units[0]->payments->captures[0]->invoice_id, 'merchant_response_successful' => true, 'merchant_response_date' => date('Y-m-d'), 'merchant_response_driver' => 'paypal', 'merchant_response' => 'name: ' . $response_error->name . '; details: ' . $response_error->details[0]->description, 'merchant_response_reference' => 'Resource ID:' . $order_id]);
            //set error message to be flashed
            $this->session->set_flashdata('alert_error', trans('online_payment_payment_failed') . '<br>' . $response_error->details[0]->description);
            $this->session->keep_flashdata('alert_error');
        }
    }
    /**
     * @originalName createClient
     *
     * @originalFile PaypalController.php
     */
    protected function createClient(): void
    {
        $this->load->library('crypt');
        //load the REST API consumer library
        $this->load->library('gateways/PaypalLib', ['client_id' => get_setting('gateway_paypal_clientId'), 'client_secret' => $this->crypt->decode(get_setting('gateway_paypal_clientSecret')), 'demo' => get_setting('gateway_paypal_testMode') == 1], 'lib_paypal');
    }
}
