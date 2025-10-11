<?php

namespace Modules\Guest\Controllers;

use AllowDynamicProperties;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Modules\Core\Controllers\BaseController;
use Modules\Invoices\Services\InvoicesService;
use Modules\PaymentMethods\Services\PaymentMethodsService;
use Modules\Settings\Services\SettingsService;

#[AllowDynamicProperties]
class PaymentInformation extends BaseController
{
    /**
     * Initialize a new PaymentInformation controller instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Prepare and render the guest payment information page for a given invoice.
     *
     * Finds the invoice by its URL key, determines whether the payment form should be disabled,
     * collects available payment gateway drivers appropriate for the invoice, resolves the
     * invoice's payment method (if any), determines overdue status, and returns the view
     * populated with the prepared data.
     *
     * @param string      $invoice_url_key  the public URL key identifying the invoice
     * @param string|null $payment_provider optional gateway driver name to preselect
     *
     * @return \Illuminate\View\View The rendered guest payment information view populated with:
     *                               - disable_form (bool),
     *                               - invoice (object),
     *                               - Gateways (array of available driver names),
     *                               - payment_method (object|null),
     *                               - is_overdue (bool),
     *                               - invoice_url_key (string),
     *                               - payment_provider (string|null)
     */
    public function form($invoice_url_key, $payment_provider = null)
    {
        $disable_form = false;
        $invoice      = (new InvoicesService())->where('ip_invoices.invoice_url_key', $invoice_url_key)->get()->row();
        if ( ! $invoice) {
            Session::flash('alert_error', trans('invoice_not_found'));

            return redirect()->route('guest');
        }
        if ($invoice->invoice_balance == 0) {
            if (Session::get('user_id')) {
                Session::flash('alert_info', trans('invoice_already_paid'));

                return redirect()->route('guest');
            }
            $disable_form = true;
        }
        $gateways          = Config::get('payment_gateways');
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
        if (count($available_drivers) == 1) {
            $payment_provider = $available_drivers[0];
        }
        $payment_method = (new PaymentMethodsService())->where('payment_method_id', $invoice->payment_method)->get()->row();
        if ($invoice->payment_method == 0) {
            $payment_method = null;
        }
        $is_overdue = $invoice->invoice_balance > 0 && strtotime($invoice->invoice_date_due) < time();
        $data       = [
            'disable_form'     => $disable_form,
            'invoice'          => $invoice,
            'Gateways'         => $available_drivers,
            'payment_method'   => $payment_method,
            'is_overdue'       => $is_overdue,
            'invoice_url_key'  => $invoice_url_key,
            'payment_provider' => $payment_provider,
        ];

        return view('guest.payment_information', $data);
    }

    /**
     * Prepare data and render the Stripe payment gateway view for the given invoice URL key.
     *
     * @param string $invoice_url_key invoice URL key identifying the invoice to pay
     *
     * @return \Illuminate\View\View the Stripe gateway view populated with the public API key and the invoice URL key
     */
    public function stripe($invoice_url_key)
    {
        // GetController the api key for which the card token must be generated
        $data = ['stripe_api_key' => get_setting('gateway_stripe_apiKeyPublic'), 'invoice_url_key' => $invoice_url_key];

        return view('guest.Gateways.stripe', $data);
    }

    /**
     * Render the PayPal gateway payment view for the given invoice URL key.
     *
     * @param string $invoice_url_key the invoice's public URL key
     *
     * @return \Illuminate\View\View the rendered PayPal gateway view populated with client ID, invoice URL key, and currency
     */
    public function paypal($invoice_url_key)
    {
        $data = ['paypal_client_id' => get_setting('gateway_paypal_clientId'), 'invoice_url_key' => $invoice_url_key, 'currency' => (new SettingsService())->setting('gateway_paypal_currency')];

        return view('guest.Gateways.paypal', $data);
    }
}
