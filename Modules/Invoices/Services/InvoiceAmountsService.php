<?php

namespace Modules\Invoices\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;

#[AllowDynamicProperties]
class InvoiceAmountsService extends BaseService
{
    /**
     * @var int
     */
    public $decimal_places = 2;

    /**
     * Instantiate the service and initialize invoice tax decimal precision.
     *
     * Initializes the InvoiceAmountsService with its required dependencies and sets
     * the decimal precision for tax-related calculations from the `tax_rate_decimal_places` setting.
     *
     * @param InvoicesService $invoicesService Service for invoice operations.
     * @param InvoiceTaxRatesService $invoiceTaxRatesService Service for invoice tax rate operations.
     */
    public function __construct(
        public InvoicesService $invoicesService,
        public InvoiceTaxRatesService $invoiceTaxRatesService
    ) {
        $this->decimal_places = (int) get_setting('tax_rate_decimal_places');
        parent::__construct();
    }

    /**
     * Recalculates and persists core invoice amounts (subtotals, taxes, total, paid, balance) for a given invoice.
     *
     * Recomputes item subtotals and taxes, applies legacy or global discounts, updates or inserts the ip_invoice_amounts row,
     * triggers invoice tax recalculation, and sets invoice status/read-only flags when the invoice becomes fully paid.
     *
     * @param int $invoice_id The invoice identifier.
     * @param array $global_discount Associative array of global discount values; expects an 'item' key for the per-item global discount amount.
     */
    public function calculate($invoice_id, $global_discount)
    {
        // GetController the basic totals
        $query = $this->db->query('
            SELECT  SUM(item_subtotal) AS invoice_item_subtotal,
                    SUM(item_tax_total) AS invoice_item_tax_total,
                    SUM(item_subtotal) + SUM(item_tax_total) AS invoice_total,
                    SUM(item_discount) AS invoice_item_discount
            FROM ip_invoice_item_amounts
            WHERE item_id IN (
                SELECT item_id FROM ip_invoice_items WHERE invoice_id = ' . $this->db->escape($invoice_id) . '
            )
        ');
        $invoice_amounts = $query->row();
        // Discounts calculation - since v1.6.3
        if (config_item('legacy_calculation')) {
            $invoice_item_subtotal = $invoice_amounts->invoice_item_subtotal - $invoice_amounts->invoice_item_discount;
            $invoice_subtotal      = $invoice_item_subtotal + $invoice_amounts->invoice_item_tax_total;
            $invoice_total         = $this->calculateDiscount($invoice_id, $invoice_subtotal);
        } else {
            $invoice_item_subtotal = $invoice_amounts->invoice_item_subtotal - $invoice_amounts->invoice_item_discount - $global_discount['item'];
            $invoice_total         = $invoice_item_subtotal + $invoice_amounts->invoice_item_tax_total;
        }
        // GetController the amount already paid
        $query = $this->db->query('
          SELECT SUM(payment_amount) AS invoice_paid
          FROM ip_payments
          WHERE invoice_id = ' . $this->db->escape($invoice_id));
        $invoice_paid = $query->row()->invoice_paid ? (float) $query->row()->invoice_paid : 0;
        // Create the database array and insert or update
        $db_array = ['invoice_id' => $invoice_id, 'invoice_item_subtotal' => $invoice_item_subtotal, 'invoice_item_tax_total' => $invoice_amounts->invoice_item_tax_total, 'invoice_total' => $invoice_total, 'invoice_paid' => $invoice_paid, 'invoice_balance' => $invoice_total - $invoice_paid];
        $this->db->where('invoice_id', $invoice_id);
        if ($this->db->get('ip_invoice_amounts')->numRows()) {
            // The record already exists; update it
            $this->db->where('invoice_id', $invoice_id);
            $this->db->update('ip_invoice_amounts', $db_array);
        } else {
            // The record does not yet exist; insert it
            $this->db->insert('ip_invoice_amounts', $db_array);
        }
        // Calculate the invoice taxes
        $this->calculateInvoiceTaxes($invoice_id);
        // GetController invoice status
        $invoice           = $this->invoicesService->getById($invoice_id);
        $invoice_is_credit = $invoice->creditinvoice_parent_id > 0;
        // Set to paid if balance is zero
        // Check if the invoice total is not zero or negative
        if ($invoice->invoice_balance == 0 && ($invoice->invoice_total != 0 || $invoice_is_credit)) {
            $this->db->where('invoice_id', $invoice_id);
            $payment           = $this->db->get('ip_payments')->row();
            $payment_method_id = $payment->payment_method_id ? $payment->payment_method_id : 0;
            $this->db->where('invoice_id', $invoice_id);
            $this->db->set('invoice_status_id', 4);
            $this->db->set('payment_method', $payment_method_id);
            $this->db->update('ip_invoices');
            // Set to read-only if applicable
            if ($this->config->item('disable_read_only') == false && $invoice->invoice_status_id == get_setting('read_only_toggle')) {
                $this->db->where('invoice_id', $invoice_id);
                $this->db->set('is_read_only', 1);
                $this->db->update('ip_invoices');
            }
        }
    }

    /**
     * @originalName calculateDiscount
     *
     * @originalFile InvoiceAmount.php
     */
    public function calculateDiscount($invoice_id, $invoice_total)
    {
        $this->db->where('invoice_id', $invoice_id);
        $invoice_data = $this->db->get('ip_invoices')->row();
        // Prevent NULL in number_format
        $total            = (float) number_format((float) $invoice_total, $this->decimal_places, '.', '');
        $discount_amount  = (float) number_format((float) $invoice_data->invoice_discount_amount, $this->decimal_places, '.', '');
        $discount_percent = (float) number_format((float) $invoice_data->invoice_discount_percent, $this->decimal_places, '.', '');
        $total -= $discount_amount;

        return $total - round($total / 100 * $discount_percent, $this->decimal_places);
    }

    /**
     * @originalName getGlobalDiscount
     *
     * @originalFile InvoiceAmount.php
     */
    public function getGlobalDiscount($invoice_id)
    {
        $row = $this->db->query('
            SELECT SUM(item_subtotal) - (SUM(item_total) - SUM(item_tax_total) + SUM(item_discount)) AS global_discount
            FROM ip_invoice_item_amounts
            WHERE item_id
                IN (SELECT item_id FROM ip_invoice_items WHERE invoice_id = ' . $this->db->escape($invoice_id) . ')
            ')->row();

        return $row->global_discount;
    }

    /**
     * Recalculate invoice-level taxes and persist updated tax totals, invoice total, and balance for an invoice.
     *
     * When invoice-level tax rates exist, computes each tax amount (including item tax when configured),
     * updates ip_invoice_tax_rates and ip_invoice_amounts with the summed invoice tax total, and recalculates
     * the invoice total and balance. If legacy calculation mode is enabled, applies the invoice discount as part
     * of the total recalculation. If no invoice-level taxes are present, sets the invoice tax total to 0.00.
     *
     * @param int $invoice_id The ID of the invoice to recalculate taxes for.
     */
    public function calculateInvoiceTaxes($invoice_id)
    {
        // First check to see if there are any invoice taxes applied
        $invoice_tax_rates = config_item('legacy_calculation') ? $this->invoiceTaxRatesService->where('invoice_id', $invoice_id)->get()->result() : null;
        if ($invoice_tax_rates) {
            // There are invoice taxes applied
            // GetController the current invoice amount record
            $invoice_amount = $this->db->where('invoice_id', $invoice_id)->get('ip_invoice_amounts')->row();
            // Loop through the invoice taxes and update the amount for each of the applied invoice taxes
            foreach ($invoice_tax_rates as $invoice_tax_rate) {
                if ($invoice_tax_rate->include_item_tax) {
                    // The invoice tax rate should include the applied item tax
                    $invoice_tax_rate_amount = ($invoice_amount->invoice_item_subtotal + $invoice_amount->invoice_item_tax_total) * ($invoice_tax_rate->invoice_tax_rate_percent / 100);
                } else {
                    // The invoice tax rate should not include the applied item tax
                    $invoice_tax_rate_amount = $invoice_amount->invoice_item_subtotal * ($invoice_tax_rate->invoice_tax_rate_percent / 100);
                }
                // Update the invoice tax rate record
                $db_array = ['invoice_tax_rate_amount' => $invoice_tax_rate_amount];
                $this->db->where('invoice_tax_rate_id', $invoice_tax_rate->invoice_tax_rate_id);
                $this->db->update('ip_invoice_tax_rates', $db_array);
            }
            // Update the invoice amount record with the total invoice tax amount
            $this->db->query('
              UPDATE ip_invoice_amounts
              SET invoice_tax_total = (
                SELECT SUM(invoice_tax_rate_amount)
                FROM ip_invoice_tax_rates
                WHERE invoice_id = ' . $this->db->escape($invoice_id) . ')
              WHERE invoice_id = ' . $this->db->escape($invoice_id));
            // GetController the updated invoice amount record
            $invoice_amount = $this->db->where('invoice_id', $invoice_id)->get('ip_invoice_amounts')->row();
            // Recalculate the invoice total and balance
            $invoice_total = $invoice_amount->invoice_item_subtotal + $invoice_amount->invoice_item_tax_total + $invoice_amount->invoice_tax_total;
            // Legacy calculation need recalculate global discounts - New calculation not! & deactivated before here - Only for memo - Todo?: idea settings: calculation mode - since v1.6.3
            if (config_item('legacy_calculation')) {
                $invoice_total = $this->calculateDiscount($invoice_id, $invoice_total);
            }
            $invoice_balance = $invoice_total - $invoice_amount->invoice_paid;
            // Update the invoice amount record
            $db_array = ['invoice_total' => $invoice_total, 'invoice_balance' => $invoice_balance];
            $this->db->where('invoice_id', $invoice_id);
            $this->db->update('ip_invoice_amounts', $db_array);
        } else {
            // No invoice taxes applied
            $db_array = ['invoice_tax_total' => '0.00'];
            $this->db->where('invoice_id', $invoice_id);
            $this->db->update('ip_invoice_amounts', $db_array);
        }
    }

    /**
     * @originalName getTotalInvoiced
     *
     * @originalFile InvoiceAmount.php
     */
    public function getTotalInvoiced($period = null)
    {
        switch ($period) {
            case 'month':
                return $this->db->query('
                    SELECT SUM(invoice_total) AS total_invoiced
                    FROM ip_invoice_amounts
                    WHERE invoice_id IN
                    (SELECT invoice_id FROM ip_invoices
                    WHERE MONTH(invoice_date_created) = MONTH(NOW())
                    AND YEAR(invoice_date_created) = YEAR(NOW()))')->row()->total_invoiced;
            case 'last_month':
                return $this->db->query('
                    SELECT SUM(invoice_total) AS total_invoiced
                    FROM ip_invoice_amounts
                    WHERE invoice_id IN
                    (SELECT invoice_id FROM ip_invoices
                    WHERE MONTH(invoice_date_created) = MONTH(NOW() - INTERVAL 1 MONTH)
                    AND YEAR(invoice_date_created) = YEAR(NOW() - INTERVAL 1 MONTH))')->row()->total_invoiced;
            case 'year':
                return $this->db->query('
                    SELECT SUM(invoice_total) AS total_invoiced
                    FROM ip_invoice_amounts
                    WHERE invoice_id IN
                    (SELECT invoice_id FROM ip_invoices WHERE YEAR(invoice_date_created) = YEAR(NOW()))')->row()->total_invoiced;
            case 'last_year':
                return $this->db->query('
                    SELECT SUM(invoice_total) AS total_invoiced
                    FROM ip_invoice_amounts
                    WHERE invoice_id IN
                    (SELECT invoice_id FROM ip_invoices WHERE YEAR(invoice_date_created) = YEAR(NOW() - INTERVAL 1 YEAR))')->row()->total_invoiced;
            default:
                return $this->db->query('SELECT SUM(invoice_total) AS total_invoiced FROM ip_invoice_amounts')->row()->total_invoiced;
        }
    }

    /**
     * @originalName getTotalPaid
     *
     * @originalFile InvoiceAmount.php
     */
    public function getTotalPaid($period = null)
    {
        switch ($period) {
            case 'month':
                return $this->db->query('
                    SELECT SUM(invoice_paid) AS total_paid
                    FROM ip_invoice_amounts
                    WHERE invoice_id IN
                    (SELECT invoice_id FROM ip_invoices
                    WHERE MONTH(invoice_date_created) = MONTH(NOW())
                    AND YEAR(invoice_date_created) = YEAR(NOW()))')->row()->total_paid;
            case 'last_month':
                return $this->db->query('SELECT SUM(invoice_paid) AS total_paid
                    FROM ip_invoice_amounts
                    WHERE invoice_id IN
                    (SELECT invoice_id FROM ip_invoices
                    WHERE MONTH(invoice_date_created) = MONTH(NOW() - INTERVAL 1 MONTH)
                    AND YEAR(invoice_date_created) = YEAR(NOW() - INTERVAL 1 MONTH))')->row()->total_paid;
            case 'year':
                return $this->db->query('SELECT SUM(invoice_paid) AS total_paid
                    FROM ip_invoice_amounts
                    WHERE invoice_id IN
                    (SELECT invoice_id FROM ip_invoices WHERE YEAR(invoice_date_created) = YEAR(NOW()))')->row()->total_paid;
            case 'last_year':
                return $this->db->query('SELECT SUM(invoice_paid) AS total_paid
                    FROM ip_invoice_amounts
                    WHERE invoice_id IN
                    (SELECT invoice_id FROM ip_invoices WHERE YEAR(invoice_date_created) = YEAR(NOW() - INTERVAL 1 YEAR))')->row()->total_paid;
            default:
                return $this->db->query('SELECT SUM(invoice_paid) AS total_paid FROM ip_invoice_amounts')->row()->total_paid;
        }
    }

    /**
     * @originalName getTotalBalance
     *
     * @originalFile InvoiceAmount.php
     */
    public function getTotalBalance($period = null)
    {
        switch ($period) {
            case 'month':
                return $this->db->query('SELECT SUM(invoice_balance) AS total_balance
                    FROM ip_invoice_amounts
                    WHERE invoice_id IN
                    (SELECT invoice_id FROM ip_invoices
                    WHERE MONTH(invoice_date_created) = MONTH(NOW())
                    AND YEAR(invoice_date_created) = YEAR(NOW()))')->row()->total_balance;
            case 'last_month':
                return $this->db->query('SELECT SUM(invoice_balance) AS total_balance
                    FROM ip_invoice_amounts
                    WHERE invoice_id IN
                    (SELECT invoice_id FROM ip_invoices
                    WHERE MONTH(invoice_date_created) = MONTH(NOW() - INTERVAL 1 MONTH)
                    AND YEAR(invoice_date_created) = YEAR(NOW() - INTERVAL 1 MONTH))')->row()->total_balance;
            case 'year':
                return $this->db->query('SELECT SUM(invoice_balance) AS total_balance
                    FROM ip_invoice_amounts
                    WHERE invoice_id IN
                    (SELECT invoice_id FROM ip_invoices WHERE YEAR(invoice_date_created) = YEAR(NOW()))')->row()->total_balance;
            case 'last_year':
                return $this->db->query('SELECT SUM(invoice_balance) AS total_balance
                    FROM ip_invoice_amounts
                    WHERE invoice_id IN
                    (SELECT invoice_id FROM ip_invoices WHERE YEAR(invoice_date_created) = (YEAR(NOW() - INTERVAL 1 YEAR)))')->row()->total_balance;
            default:
                return $this->db->query('SELECT SUM(invoice_balance) AS total_balance FROM ip_invoice_amounts')->row()->total_balance;
        }
    }

    /**
     * @originalName getStatusTotals
     *
     * @originalFile InvoiceAmount.php
     */
    public function getStatusTotals($period = '')
    {
        switch ($period) {
            default:
            case 'this-month':
                $results = $this->db->query('
                    SELECT ip_invoices.invoice_status_id, (CASE ip_invoices.invoice_status_id WHEN 4 THEN SUM(ip_invoice_amounts.invoice_paid) ELSE SUM(ip_invoice_amounts.invoice_balance) END) AS sum_total, COUNT(*) AS num_total
                    FROM ip_invoice_amounts
                    JOIN ip_invoices ON ip_invoices.invoice_id = ip_invoice_amounts.invoice_id
                        AND MONTH(ip_invoices.invoice_date_created) = MONTH(NOW())
                        AND YEAR(ip_invoices.invoice_date_created) = YEAR(NOW())
                    GROUP BY ip_invoices.invoice_status_id')->resultArray();
                break;
            case 'last-month':
                $results = $this->db->query('
                    SELECT invoice_status_id, (CASE ip_invoices.invoice_status_id WHEN 4 THEN SUM(invoice_paid) ELSE SUM(invoice_balance) END) AS sum_total, COUNT(*) AS num_total
                    FROM ip_invoice_amounts
                    JOIN ip_invoices ON ip_invoices.invoice_id = ip_invoice_amounts.invoice_id
                        AND MONTH(ip_invoices.invoice_date_created) = MONTH(NOW() - INTERVAL 1 MONTH)
                        AND YEAR(ip_invoices.invoice_date_created) = YEAR(NOW())
                    GROUP BY ip_invoices.invoice_status_id')->resultArray();
                break;
            case 'this-quarter':
                $results = $this->db->query('
                    SELECT invoice_status_id, (CASE ip_invoices.invoice_status_id WHEN 4 THEN SUM(ip_invoice_amounts.invoice_paid) ELSE SUM(ip_invoice_amounts.invoice_balance) END) AS sum_total, COUNT(*) AS num_total
                    FROM ip_invoice_amounts
                    JOIN ip_invoices ON ip_invoices.invoice_id = ip_invoice_amounts.invoice_id
                        AND QUARTER(ip_invoices.invoice_date_created) = QUARTER(NOW())
                        AND YEAR(ip_invoices.invoice_date_created) = YEAR(NOW())
                    GROUP BY ip_invoices.invoice_status_id')->resultArray();
                break;
            case 'last-quarter':
                $results = $this->db->query('
                    SELECT invoice_status_id, (CASE ip_invoices.invoice_status_id WHEN 4 THEN SUM(invoice_paid) ELSE SUM(invoice_balance) END) AS sum_total, COUNT(*) AS num_total
                    FROM ip_invoice_amounts
                    JOIN ip_invoices ON ip_invoices.invoice_id = ip_invoice_amounts.invoice_id
                        AND QUARTER(ip_invoices.invoice_date_created) = QUARTER(NOW() - INTERVAL 1 QUARTER)
                        AND YEAR(ip_invoices.invoice_date_created) = YEAR(NOW())
                    GROUP BY ip_invoices.invoice_status_id')->resultArray();
                break;
            case 'this-year':
                $results = $this->db->query('
                    SELECT invoice_status_id, (CASE ip_invoices.invoice_status_id WHEN 4 THEN SUM(ip_invoice_amounts.invoice_paid) ELSE SUM(ip_invoice_amounts.invoice_balance) END) AS sum_total, COUNT(*) AS num_total
                    FROM ip_invoice_amounts
                    JOIN ip_invoices ON ip_invoices.invoice_id = ip_invoice_amounts.invoice_id
                        AND YEAR(ip_invoices.invoice_date_created) = YEAR(NOW())
                    GROUP BY ip_invoices.invoice_status_id')->resultArray();
                break;
            case 'last-year':
                $results = $this->db->query('
                    SELECT invoice_status_id, (CASE ip_invoices.invoice_status_id WHEN 4 THEN SUM(invoice_paid) ELSE SUM(invoice_balance) END) AS sum_total, COUNT(*) AS num_total
                    FROM ip_invoice_amounts
                    JOIN ip_invoices ON ip_invoices.invoice_id = ip_invoice_amounts.invoice_id
                        AND YEAR(ip_invoices.invoice_date_created) = YEAR(NOW() - INTERVAL 1 YEAR)
                    GROUP BY ip_invoices.invoice_status_id')->resultArray();
                break;
        }
        $return = [];
        foreach ($this->mdl_invoices->statuses() as $key => $status) {
            $return[$key] = ['invoice_status_id' => $key, 'class' => $status['class'], 'label' => $status['label'], 'href' => $status['href'], 'sum_total' => 0, 'num_total' => 0];
        }
        foreach ($results as $result) {
            $return[$result['invoice_status_id']] = array_merge($return[$result['invoice_status_id']], $result);
        }

        return $return;
    }
}