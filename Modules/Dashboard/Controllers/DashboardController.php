<?php

namespace Modules\Dashboard\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\Invoices\Services\InvoiceAmountsService;
use Modules\Invoices\Services\InvoicesService;
use Modules\Projects\Services\ProjectsService;
use Modules\Quotes\Services\QuoteAmountsService;
use Modules\Quotes\Services\QuotesService;
use Modules\Tasks\Services\TasksService;

#[AllowDynamicProperties]
class DashboardController extends AdminController
{
    /**
     * Prepare data required by the admin dashboard and render the dashboard view.
     *
     * The view data includes:
     * - `invoice_status_totals`, `quote_status_totals`
     * - `invoice_status_period`, `quote_status_period`
     * - `invoices`, `quotes`
     * - `invoice_statuses`, `quote_statuses`
     * - `overdue_invoices`
     * - `projects`, `tasks`, `task_statuses`
     *
     * @return string The rendered dashboard view content. */
    public function index()
    {
        $quote_overview_period   = get_setting('quote_overview_period');
        $invoice_overview_period = get_setting('invoice_overview_period');

        return view('dashboard.index', ['invoice_status_totals' => (new InvoiceAmountsService())->getStatusTotals($invoice_overview_period), 'quote_status_totals' => (new QuoteAmountsService())->getStatusTotals($quote_overview_period), 'invoice_status_period' => str_replace('-', '_', $invoice_overview_period), 'quote_status_period' => str_replace('-', '_', $quote_overview_period), 'invoices' => (new InvoicesService())->limit(10)->get()->result(), 'quotes' => (new QuotesService())->limit(10)->get()->result(), 'invoice_statuses' => (new InvoicesService())->statuses(), 'quote_statuses' => (new QuotesService())->statuses(), 'overdue_invoices' => (new InvoicesService())->isOverdue()->get()->result(), 'projects' => (new ProjectsService())->getLatest()->get()->result(), 'tasks' => (new TasksService())->getLatest()->get()->result(), 'task_statuses' => (new TasksService())->statuses()]);
    }
}