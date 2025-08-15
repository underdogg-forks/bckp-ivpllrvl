<?php

namespace Modules\Dashboard\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class DashboardController extends AdminController
{
    /**
     * @originalName index
     *
     * @originalFile DashboardController.php
     */
    public function index()
    {
        $this->load->model('invoices/mdl_invoice_amounts');
        $this->load->model('quotes/mdl_quote_amounts');
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('projects/mdl_projects');
        $this->load->model('tasks/mdl_tasks');
        $quote_overview_period   = get_setting('quote_overview_period');
        $invoice_overview_period = get_setting('invoice_overview_period');

        return view('dashboard.index', ['invoice_status_totals' => (new InvoiceAmountsService())->getStatusTotals($invoice_overview_period), 'quote_status_totals' => (new QuoteAmountsService())->getStatusTotals($quote_overview_period), 'invoice_status_period' => str_replace('-', '_', $invoice_overview_period), 'quote_status_period' => str_replace('-', '_', $quote_overview_period), 'invoices' => (new InvoicesService())->limit(10)->get()->result(), 'quotes' => (new QuotesService())->limit(10)->get()->result(), 'invoice_statuses' => (new InvoicesService())->statuses(), 'quote_statuses' => (new QuotesService())->statuses(), 'overdue_invoices' => (new InvoicesService())->isOverdue()->get()->result(), 'projects' => (new ProjectsService())->getLatest()->get()->result(), 'tasks' => (new TasksService())->getLatest()->get()->result(), 'task_statuses' => (new TasksService())->statuses()]);
    }
}
