<?php

namespace Modules\Payments\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Models\InvoiceAmount;
use Modules\Payments\Models\Payment;

#[AllowDynamicProperties]
class PaymentsService extends BaseService
{
    public $table = 'ip_payments';

    public $primary_key = 'ip_payments.payment_id';

    public $validation_rules = 'validation_rules';

    /**
     * Get a base Payment query with relationships for select.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultSelect(): \Illuminate\Database\Eloquent\Builder
    {
        return Payment::query()->with(['invoice.invoiceAmount', 'invoice.client', 'paymentMethod']);
    }

    /**
     * Get a Payment query ordered by payment date and id.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultOrderBy(): \Illuminate\Database\Eloquent\Builder
    {
        return Payment::query()->orderByDesc('payment_date')->orderByDesc('payment_id');
    }

    /**
     * Get a Payment query with relationships (joins).
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultJoin(): \Illuminate\Database\Eloquent\Builder
    {
        return Payment::query()->with(['invoice.invoiceAmount', 'invoice.client', 'paymentMethod']);
    }

    /**
     * Validate payment amount using Eloquent.
     *
     * @param float $amount
     * @param int $invoice_id
     * @param int|null $payment_id
     * @return bool
     */
    public function validatePaymentAmount(float $amount, int $invoice_id, ?int $payment_id = null): bool
    {
        $amount = (float) standardize_amount($amount);
        $invoice = InvoiceAmount::query()->where('invoice_id', $invoice_id)->first();
        if ($invoice === null) {
            return false;
        }
        $invoice_balance = (float) $invoice->invoice_balance;
        if ($payment_id) {
            $payment = Payment::query()->find($payment_id);
            if ($payment) {
                $invoice_balance += (float) $payment->payment_amount;
            }
        }
        return $amount <= $invoice_balance;
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
        $id                      = parent::save($id, $db_array);
        $global_discount['item'] = $this->mdl_invoice_amounts->getGlobalDiscount($db_array['invoice_id']);
        // Recalculate invoice amounts
        $this->mdl_invoice_amounts->calculate($db_array['invoice_id'], $global_discount);
        // Set proper status for the invoice
        $invoice = InvoiceAmount::query()->where('invoice_id', $db_array['invoice_id'])->first();
        if ($invoice == null) {
            return false;
        }
        // Calculate sum for payments
        $paid  = (float) $invoice->invoice_paid;
        $total = (float) $invoice->invoice_total;
        if ($paid >= $total) {
            Invoice::query()->where('invoice_id', $db_array['invoice_id'])->update(['invoice_status_id' => 4]);
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
        $db_array                   = parent::dbArray();
        $db_array['payment_date']   = date_to_mysql($db_array['payment_date']);
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
        $payment = Payment::query()->select('invoice_id')->where('payment_id', $id)->first();
        $invoice_id = $payment->invoice_id;
        
        // Delete the payment
        parent::delete($id);
        $this->load->model('invoices/mdl_invoice_amounts');
        $global_discount['item'] = $this->mdl_invoice_amounts->getGlobalDiscount($invoice_id);
        // Recalculate invoice amounts
        $this->mdl_invoice_amounts->calculate($invoice_id, $global_discount);
        // Change invoice status back to sent
        $invoice = Invoice::query()->select('invoice_status_id')->where('invoice_id', $invoice_id)->first();
        if ($invoice->invoice_status_id == 4) {
            Invoice::query()->where('invoice_id', $invoice_id)->update(['invoice_status_id' => 2]);
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
        if ( ! parent::prepForm($id)) {
            return false;
        }
        if ( ! $id) {
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
