<?php

namespace Modules\InvoiceGroups\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\InvoiceGroups\Services\InvoiceGroupsService;

#[AllowDynamicProperties]
class InvoiceGroupsController extends AdminController
{
    /**
     * Create a new InvoiceGroupsController instance and perform controller initialization.
     *
     * Calls the parent controller constructor to inherit base setup required for admin controllers.
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
        if ($this->input->post('btn_cancel')) {
            redirect()->route('invoice_groups');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ((new InvoiceGroupsService())->runValidation()) {
            (new InvoiceGroupsService())->save($id);
            redirect()->route('invoice_groups');
        }
        if ($id && ! $this->input->post('btn_submit')) {
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
     * @originalName delete
     *
     * @originalFile InvoiceGroupsController.php
     */
    public function delete($id)
    {
        (new InvoiceGroupsService())->delete($id);
        redirect()->route('invoice_groups');
    }
}