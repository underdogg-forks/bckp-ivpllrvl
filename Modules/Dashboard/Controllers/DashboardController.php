<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Dashboard\Controllers;

use Modules\Core\Controllers\AdminController;
/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */
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
        $quote_overview_period = get_setting('quote_overview_period');
        $invoice_overview_period = get_setting('invoice_overview_period');
        $this->layout->set(['invoice_status_totals' => $this->mdl_invoice_amounts->getStatusTotals($invoice_overview_period), 'quote_status_totals' => $this->mdl_quote_amounts->getStatusTotals($quote_overview_period), 'invoice_status_period' => str_replace('-', '_', $invoice_overview_period), 'quote_status_period' => str_replace('-', '_', $quote_overview_period), 'invoices' => $this->mdl_invoices->limit(10)->get()->result(), 'quotes' => $this->mdl_quotes->limit(10)->get()->result(), 'invoice_statuses' => $this->mdl_invoices->statuses(), 'quote_statuses' => $this->mdl_quotes->statuses(), 'overdue_invoices' => $this->mdl_invoices->isOverdue()->get()->result(), 'projects' => $this->mdl_projects->getLatest()->get()->result(), 'tasks' => $this->mdl_tasks->getLatest()->get()->result(), 'task_statuses' => $this->mdl_tasks->statuses()]);
        $this->layout->buffer('content', 'dashboard/index');
        $this->layout->render();
    }
}
