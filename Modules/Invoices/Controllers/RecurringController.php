<?php

namespace Modules\Invoices\Controllers;

use Illuminate\Contracts\View\View;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\Invoices\Services\InvoicesRecurringService;

#[AllowDynamicProperties]
class RecurringController extends AdminController
{
    /**
     * Create a RecurringController with its dependencies.
     *
     * @param InvoicesRecurringService $invoicesRecurringService service used to manage recurring invoices
     */
    public function __construct(public InvoicesRecurringService $invoicesRecurringService)
    {
        parent::__construct();
    }

    /**
     * Display the recurring invoices listing page.
     *
     * Passes pagination results and related view data to the recurring invoices view.
     *
     * @param int $page page index for pagination (defaults to 0)
     *
     * @return string rendered view for the recurring invoices list containing:
     *                - `filter_display`: whether to show the filter controls,
     *                - `filter_placeholder`: translated placeholder for the filter,
     *                - `filter_method`: JS filter method name,
     *                - `recur_frequencies`: available recurrence frequencies,
     *                - `recurring_invoices`: paginated recurring invoice results
     */
    public function index($page = 0): View
    {
        $this->invoicesRecurringService->paginate(site_url('invoices/recurring'), $page);
        $recurring_invoices = $this->invoicesRecurringService->result();

        return view('invoices.index_recurring', ['filter_display' => true, 'filter_placeholder' => trans('filter_invoices_recuring'), 'filter_method' => 'filter_invoices_recuring', 'recur_frequencies' => $this->invoicesRecurringService->recur_frequencies, 'recurring_invoices' => $recurring_invoices]);
    }

    /**
     * Stop a recurring invoice and redirect to the recurring invoices index.
     *
     * @param int|string $invoice_recurring_id the ID of the recurring invoice to stop
     *
     * @return \Illuminate\Http\RedirectResponse a redirect response to the recurring invoices index route
     */
    public function stop($invoice_recurring_id)
    {
        $this->invoicesRecurringService->stop($invoice_recurring_id);

        return redirect()->route('invoices/recurring/index');
    }

    /**
     * Deletes a recurring invoice and redirects to the recurring invoices index.
     *
     * @param int|string $invoice_recurring_id the ID of the recurring invoice to delete
     */
    public function delete($invoice_recurring_id)
    {
        $this->invoicesRecurringService->delete($invoice_recurring_id);
        redirect()->route('invoices/recurring/index');
    }
}
