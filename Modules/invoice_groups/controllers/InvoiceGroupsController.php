<?php

namespace Modules\Invoicegroups\Controllers;

use Modules\Core\Controllers\AdminController;

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */
#[AllowDynamicProperties]
class InvoiceGroupsController extends AdminController
{
    /**
     * Invoice_Groups constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_invoice_groups');
    }

    /**
     * @originalName index
     *
     * @originalFile InvoiceGroupsController.php
     */
    public function index($page = 0)
    {
        $this->mdl_invoice_groups->paginate(site_url('invoice_groups/index'), $page);
        $invoice_groups = $this->mdl_invoice_groups->result();
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
            redirect('invoice_groups');
        }
        $this->filter_input();
        // <<<--- filters _POST array for nastiness
        if ($this->mdl_invoice_groups->run_validation()) {
            $this->mdl_invoice_groups->save($id);
            redirect('invoice_groups');
        }
        if ($id && ! $this->input->post('btn_submit')) {
            if ( ! $this->mdl_invoice_groups->prepForm($id)) {
                show_404();
            }
        } elseif ( ! $id) {
            $this->mdl_invoice_groups->set_form_value('invoice_group_left_pad', 0);
            $this->mdl_invoice_groups->set_form_value('invoice_group_next_id', 1);
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
        $this->mdl_invoice_groups->delete($id);
        redirect('invoice_groups');
    }
}
