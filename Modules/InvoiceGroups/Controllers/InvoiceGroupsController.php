<?php

namespace Modules\InvoiceGroups\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\InvoiceGroups\Services\InvoiceGroupsService;

#[AllowDynamicProperties]
class InvoiceGroupsController extends AdminController
{
    /**
     * Initialize the InvoiceGroupsController.
     *
     * Performs controller initialization required for admin controllers.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @originalName index
     *
     * @originalFile InvoiceGroupsController.php
     */
    public function index($page = 0): View
    {
        (new InvoiceGroupsService())->paginate(site_url('invoice_groups/index'), $page);
        $invoice_groups = (new InvoiceGroupsService())->result();

        return view('invoice_groups.index', ['invoice_groups' => $invoice_groups]);
    }

    /**
     * @originalName form
     *
     * @originalFile InvoiceGroupsController.php
     */
    public function form(Request $request, $id = null): View|RedirectResponse
    {
        if ($request->has('btn_cancel')) {
            return redirect()->route('invoice_groups');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        $service = new InvoiceGroupsService();
        if ($service->runValidation('validationRules', $request)) {
            $service->save($request, $id);
            return redirect()->route('invoice_groups');
        }
        if ($id && $request->isMethod('GET')) {
            if (! $service->prepForm($id)) {
                abort(404);
            }
        } elseif (! $id) {
            $service->setFormValue('invoice_group_left_pad', 0);
            $service->setFormValue('invoice_group_next_id', 1);
        }
        return view('invoice_groups.form');
    }

    /**
     * Delete the invoice group identified by the given ID and redirect to the invoice groups list.
     *
     * @param int|string $id the ID of the invoice group to delete
     */
    public function delete($id)
    {
        (new InvoiceGroupsService())->delete($id);
        return redirect()->route('invoice_groups');
    }
}
