<?php

namespace Modules\Invoices\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class RecurringController extends AdminController
{
    /**
     * RecurringController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_invoices_recurring');
    }

    /**
     * @originalName index
     *
     * @originalFile RecurringController.php
     */
    public function index($page = 0)
    {
        (new InvoicesRecurringService())->paginate(site_url('invoices/recurring'), $page);
        $recurring_invoices = (new InvoicesRecurringService())->result();

        return view('invoices.index_recurring', ['filter_display' => true, 'filter_placeholder' => trans('filter_invoices_recuring'), 'filter_method' => 'filter_invoices_recuring', 'recur_frequencies' => (new InvoicesRecurringService())->recur_frequencies, 'recurring_invoices' => $recurring_invoices]);
    }

    /**
     * @originalName stop
     *
     * @originalFile RecurringController.php
     */
    public function stop($invoice_recurring_id)
    {
        (new InvoicesRecurringService())->stop($invoice_recurring_id);
        redirect()->route('invoices/recurring/index');
    }

    /**
     * @originalName delete
     *
     * @originalFile RecurringController.php
     */
    public function delete($invoice_recurring_id)
    {
        (new InvoicesRecurringService())->delete($invoice_recurring_id);
        redirect()->route('invoices/recurring/index');
    }
}
