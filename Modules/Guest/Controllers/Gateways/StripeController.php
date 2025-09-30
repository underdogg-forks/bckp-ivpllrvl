<?php

namespace Modules\Guest\Controllers\Gateways;

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

use AllowDynamicProperties;
use App\Models\MerchantResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Modules\Core\Controllers\BaseController;
use Modules\Invoices\Services\InvoicesService;
use Modules\Payments\Services\PaymentsService;
use Stripe\StripeClient;

#[AllowDynamicProperties]
class StripeController extends BaseController
{
    protected StripeClient $stripe;
    protected InvoicesService $invoicesService;
    protected PaymentsService $paymentsService;

    public function __construct(InvoicesService $invoicesService, PaymentsService $paymentsService)
    {
        parent::__construct();
        $this->invoicesService = $invoicesService;
        $this->paymentsService = $paymentsService;
        $this->stripe = new StripeClient($this->crypt->decode(get_setting('gateway_stripe_apiKey')));
    }

    /**
     * @originalName createCheckoutSession
     *
     * @originalFile StripeController.php
     */
    public function createCheckoutSession($invoice_url_key)
    {
        $invoice = $this->invoicesService->where('invoice_url_key', $invoice_url_key)->first();
        // Check if the invoice is payable
        if ($invoice->invoice_balance <= 0) {
            $this->session->set_userdata('alert_error', lang('invoice_already_paid'));
            redirect(site_url('guest/view/invoice/' . $invoice->invoice_url_key));
        }
        $checkout_session = $this->stripe->checkout->sessions->create([
            'mode'                => 'payment',
            'ui_mode'             => 'embedded',
            'return_url'          => site_url('guest/Gateways/stripe/callback/{CHECKOUT_SESSION_ID}'),
            'client_reference_id' => $invoice->invoice_url_key,
            // More privacy of invoice_id
            'line_items' => [['price_data' => ['currency' => get_setting('gateway_stripe_currency'), 'unit_amount' => $invoice->invoice_balance * 100, 'product_data' => ['name' => trans('invoice') . ' #' . $invoice->invoice_number]], 'quantity' => 1]],
        ]);
        $this->output->set_output(json_encode(['clientSecret' => $checkout_session->client_secret]));
    }

    /**
     * @originalName callback
     *
     * @originalFile StripeController.php
     */
    public function callback(string $checkout_session_id)
    {
        try {
            $session = $this->stripe->checkout->sessions->retrieve($checkout_session_id);
            Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' reached, status: ' . $session->status . ' payment_status: ' . $session->payment_status . ', checkout_session_id: ' . $checkout_session_id);
            $invoice_key = $session->client_reference_id;
            // Retrieve the invoice
            $invoice = $this->invoicesService->findByUrlKey($invoice_key);
            $paid = $session->payment_status === $session::PAYMENT_STATUS_PAID;
            if ($paid) {
                $this->paymentsService->savePayment([
                    'invoice_id' => $invoice->invoice_id,
                    'payment_date' => date('Y-m-d'),
                    'payment_amount' => $session->amount_total / 100,
                    'payment_method_id' => get_setting('gateway_stripe_payment_method'),
                    'payment_note' => trans('online_payment_intent_id') . ': ' . $session->payment_intent,
                ]);
            }
            $response = $paid
                ? '. livemode: ' . __($session->livemode ? 'yes' : 'no') . ', currency: ' . $session->currency . ', amount: ' . $session->amount_received / 100 . ', fee: ' . $session->application_fee_amount / 100 . ', session ID: ' . $session->id
                : ($session->cancel ? $session->cancellation_reason : $session->last_payment_error);
            $user_msg = $paid
                ? sprintf(__('online_payment_successful'), '#' . $invoice->invoice_number)
                : __('online_payment_failed') . '<br>' . sprintf(__('online_payment_incomplete'), __CLASS__, $session->payment_status);
        } catch (Error|Exception|ErrorException $e) {
            $user_msg = __('online_payment_error') . (empty($user_msg) ? '' : '<br>' . $user_msg);
            $paid     = 'error';
            $response = __CLASS__ . '::' . __FUNCTION__ . ' exception: ' . $e->getMessage() . (empty($response) ? '' : ' - response: ' . $response);
            Log::error(str_replace('<br>', ' ', $response . ' user_msg: ' . $user_msg));
        } finally {
            $paid = is_bool($paid) ? ($paid ? 'success' : 'info') : $paid;
            $ok = $session->status !== null;
            MerchantResponse::create([
                'invoice_id'                   => $invoice->invoice_id,
                'merchant_response_successful' => (int) $ok,
                'merchant_response_date'       => now()->toDateString(),
                'merchant_response_driver'     => __CLASS__,
                'merchant_response'            => ($ok ? $session->mode . ': ' . $session->payment_status . ', ' : '') . $response,
                'merchant_response_reference'  => $ok ? 'intent_id: ' . $session->payment_intent : 'none',
            ]);
            Session::flash('alert_' . $paid, $user_msg);
            return Redirect::to(URL::route('guest.invoice.view', ['invoice' => $invoice->invoice_url_key]));
        }
    }
}
