<?php

namespace Modules\Guest\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\BaseController;

#[AllowDynamicProperties]
class PaymentInformation extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoices/mdl_invoices');
    }

    /**
     * @originalName form
     *
     * @originalFile PaymentInformation.php
     */
    public function form($invoice_url_key, $payment_provider = null)
    {
        $this->load->model('payment_methods/mdl_payment_methods');
        $disable_form = false;
        // Check if the invoice exists and is billable
        $invoice = (new InvoicesService())->where('ip_invoices.invoice_url_key', $invoice_url_key)->get()->row();
        if ( ! $invoice) {
            $this->session->set_flashdata('alert_error', lang('invoice_not_found'));
            redirect()->route('guest');
            // /invoices
        }
        // Check if the invoice is payable
        if ($invoice->invoice_balance == 0) {
            if ($this->session->user_id) {
                $this->session->set_flashdata('alert_info', lang('invoice_already_paid'));
                redirect()->route('guest');
                // /invoices
            }
            $disable_form = true;
        }
        // GetController all payment Gateways
        $this->load->model('mdl_settings');
        $this->config->load('payment_gateways');
        $gateways          = $this->config->item('payment_gateways');
        $available_drivers = [];
        if ( ! $disable_form) {
            foreach ($gateways as $driver => $fields) {
                $d = mb_strtolower($driver);
                if (get_setting('gateway_' . $d . '_enabled') == 1) {
                    $invoice_payment_method = $invoice->payment_method;
                    $driver_payment_method  = get_setting('gateway_' . $d . '_payment_method');
                    if ($invoice_payment_method == 0 || $driver_payment_method == 0 || $driver_payment_method == $invoice_payment_method) {
                        $available_drivers[] = $driver;
                    }
                }
            }
        }
        // If only one provider is available, serve it without showing options
        if (count($available_drivers) == 1) {
            $payment_provider = $available_drivers[0];
        }
        // GetController additional invoice information
        $payment_method = (new PaymentMethodsService())->where('payment_method_id', $invoice->payment_method)->get()->row();
        if ($invoice->payment_method == 0) {
            $payment_method = null;
        }
        $is_overdue = $invoice->invoice_balance > 0 && strtotime($invoice->invoice_date_due) < time();
        // Return the view
        $data = ['disable_form' => $disable_form, 'invoice' => $invoice, 'Gateways' => $available_drivers, 'payment_method' => $payment_method, 'is_overdue' => $is_overdue, 'invoice_url_key' => $invoice_url_key, 'payment_provider' => $payment_provider];
        $this->load->view('guest/payment_information', $data) . $payment_provider && $this->{$payment_provider}($invoice_url_key);
    }

    /**
     * @originalName stripe
     *
     * @originalFile PaymentInformation.php
     */
    public function stripe($invoice_url_key)
    {
        // GetController the api key for which the card token must be generated
        $data = ['stripe_api_key' => get_setting('gateway_stripe_apiKeyPublic'), 'invoice_url_key' => $invoice_url_key];
        $this->load->view('guest/Gateways/stripe', $data);
    }

    /**
     * @originalName paypal
     *
     * @originalFile PaymentInformation.php
     */
    public function paypal($invoice_url_key)
    {
        $data = ['paypal_client_id' => get_setting('gateway_paypal_clientId'), 'invoice_url_key' => $invoice_url_key, 'currency' => (new SettingsService())->setting('gateway_paypal_currency')];
        $this->load->view('guest/Gateways/paypal', $data);
    }
}
