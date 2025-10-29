<?php

namespace Modules\Invoices\app\Http\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

use function Modules\InvoiceGroups\Controllers\redirect;
use function Modules\InvoiceGroups\Controllers\show_404;
use function Modules\InvoiceGroups\Controllers\site_url;

use Modules\Invoices\app\Services\InvoiceGroupsService;

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
    public function index($page = 0)
    {
        (new InvoiceGroupsService())->paginate(site_url('invoice_groups/index'), $page);
        $invoice_groups = (new InvoiceGroupsService())->result();
        $this->layout->set('invoice_groups', $invoice_groups);
        $this->layout->buffer('content', 'invoice_groups/index');
        $this->layout->render();
    }

    /**
     * @originalName form
     *
     * @originalFile InvoiceGroupsController.php
     */
    public function form($id = null)
    {
        if (request()->input('btn_cancel')) {
            redirect()->route('invoice_groups');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ((new InvoiceGroupsService())->runValidation()) {
            (new InvoiceGroupsService())->save($id);
            redirect()->route('invoice_groups');
        }
        if ($id && ! request()->input('btn_submit')) {
            if ( ! (new InvoiceGroupsService())->prepForm($id)) {
                show_404();
            }
        } elseif ( ! $id) {
            (new InvoiceGroupsService())->setFormValue('invoice_group_left_pad', 0);
            (new InvoiceGroupsService())->setFormValue('invoice_group_next_id', 1);
        }
        $this->layout->buffer('content', 'invoice_groups/form');
        $this->layout->render();
    }

    /**
     * Delete the invoice group identified by the given ID and redirect to the invoice groups list.
     *
     * @param int|string $id the ID of the invoice group to delete
     */
    public function delete($id)
    {
        (new InvoiceGroupsService())->delete($id);
        redirect()->route('invoice_groups');
    }
}
