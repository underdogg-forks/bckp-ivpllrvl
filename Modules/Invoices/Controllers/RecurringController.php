<?php

namespace Modules\Invoices\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\Invoices\Services\InvoicesRecurringService;

#[AllowDynamicProperties]
class RecurringController extends AdminController
{
    public function __construct(public InvoicesRecurringService $invoicesRecurringService)
    {
        parent::__construct();
    }

    /**
     * @originalName index
     *
     * @originalFile RecurringController.php
     */
    public function index($page = 0)
    {
        $this->invoicesRecurringService->paginate(site_url('invoices/recurring'), $page);
        $recurring_invoices = $this->invoicesRecurringService->result();

        return view('invoices.index_recurring', ['filter_display' => true, 'filter_placeholder' => trans('filter_invoices_recuring'), 'filter_method' => 'filter_invoices_recuring', 'recur_frequencies' => $this->invoicesRecurringService->recur_frequencies, 'recurring_invoices' => $recurring_invoices]);
    }

    /**
     * @originalName stop
     *
     * @originalFile RecurringController.php
     */
    public function stop($invoice_recurring_id)
    {
        $this->invoicesRecurringService->stop($invoice_recurring_id);

        return redirect()->route('invoices/recurring/index');
    }

    /**
     * @originalName delete
     *
     * @originalFile RecurringController.php
     */
    public function delete($invoice_recurring_id)
    {
        $this->invoicesRecurringService->delete($invoice_recurring_id);
        redirect()->route('invoices/recurring/index');
    }
}
