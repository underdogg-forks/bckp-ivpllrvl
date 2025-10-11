<?php

namespace Modules\Invoices\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\Invoices\Models\InvoiceRecurring;

#[AllowDynamicProperties]
class InvoicesRecurringService extends BaseService
{
    public $table = 'ip_invoices_recurring';

    public $primary_key = 'ip_invoices_recurring.invoice_recurring_id';

    public $recur_frequencies = ['1D' => 'calendar_day_1', '2D' => 'calendar_day_2', '3D' => 'calendar_day_3', '4D' => 'calendar_day_4', '5D' => 'calendar_day_5', '6D' => 'calendar_day_6', '15D' => 'calendar_day_15', '30D' => 'calendar_day_30', '7D' => 'calendar_week_1', '14D' => 'calendar_week_2', '21D' => 'calendar_week_3', '28D' => 'calendar_week_4', '1M' => 'calendar_month_1', '2M' => 'calendar_month_2', '3M' => 'calendar_month_3', '4M' => 'calendar_month_4', '5M' => 'calendar_month_5', '6M' => 'calendar_month_6', '7M' => 'calendar_month_7', '8M' => 'calendar_month_8', '9M' => 'calendar_month_9', '10M' => 'calendar_month_10', '11M' => 'calendar_month_11', '1Y' => 'calendar_year_1', '2Y' => 'calendar_year_2', '3Y' => 'calendar_year_3', '4Y' => 'calendar_year_4', '5Y' => 'calendar_year_5'];

    /**
     * @originalName defaultSelect
     *
     * @originalFile InvoiceRecurring.php
     */
    public function defaultSelect()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_invoices.*,
            ip_clients.client_title,
            ip_clients.client_name,
            ip_clients.client_surname,
            ip_invoices_recurring.*,
            IF(recur_end_date > date(NOW()) OR recur_end_date IS NULL, "active", "inactive") AS recur_status', false);
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile InvoiceRecurring.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_invoices', 'ip_invoices.invoice_id = ip_invoices_recurring.invoice_id');
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_invoices.client_id');
    }

    /**
     * @originalName validationRules
     *
     * @originalFile InvoiceRecurring.php
     */
    public function validationRules()
    {
        return ['invoice_id' => ['field' => 'invoice_id', 'rules' => 'required'], 'recur_start_date' => ['field' => 'recur_start_date', 'label' => trans('start_date'), 'rules' => 'required'], 'recur_end_date' => ['field' => 'recur_end_date', 'label' => trans('end_date')], 'recur_frequency' => ['field' => 'recur_frequency', 'label' => trans('every'), 'rules' => 'required']];
    }

    /**
     * @originalName dbArray
     *
     * @originalFile InvoiceRecurring.php
     */
    public function dbArray()
    {
        $db_array                     = parent::dbArray();
        $db_array['recur_start_date'] = date_to_mysql($db_array['recur_start_date']);
        $db_array['recur_next_date']  = $db_array['recur_start_date'];
        $db_array['recur_end_date']   = $db_array['recur_end_date'] ? date_to_mysql($db_array['recur_end_date']) : null;

        return $db_array;
    }

    /**
     * Marks a recurring invoice as stopped by setting its end date to today and clearing the next recurrence date.
     *
     * @param int|string $invoice_recurring_id The ID of the recurring invoice to stop.
     */
    public function stop($invoice_recurring_id)
    {
        $db_array = ['recur_end_date' => date('Y-m-d'), 'recur_next_date' => null];
        InvoiceRecurring::query()->where('invoice_recurring_id', $invoice_recurring_id)->update($db_array);
    }

    /**
     * @originalName active
     *
     * @originalFile InvoiceRecurring.php
     */
    public function active()
    {
        $this->filter_where('recur_next_date <= date(NOW()) AND (recur_end_date > date(NOW()) OR recur_end_date IS NULL)');

        return $this;
    }

    /**
     * Advance the recurring invoice's next run date by one recurrence interval.
     *
     * Loads the recurring invoice by its ID, computes the next `recur_next_date` from the current
     * `recur_next_date` and `recur_frequency`, and persists the updated `recur_next_date`.
     *
     * @param int|string $invoice_recurring_id The ID of the recurring invoice record to update.
     */
    public function setNextRecurDate($invoice_recurring_id)
    {
        $invoice_recurring = $this->where('invoice_recurring_id', $invoice_recurring_id)->get()->row();
        $recur_next_date   = increment_date($invoice_recurring->recur_next_date, $invoice_recurring->recur_frequency);
        $db_array          = ['recur_next_date' => $recur_next_date];
        InvoiceRecurring::query()->where('invoice_recurring_id', $invoice_recurring_id)->update($db_array);
    }
}