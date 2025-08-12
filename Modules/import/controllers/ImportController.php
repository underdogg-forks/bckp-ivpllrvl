<?php

namespace Modules\Import\Controllers;

use Modules\Core\Controllers\AdminController;

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */
#[AllowDynamicProperties]
class ImportController extends AdminController
{
    private array $allowed_files = ['clients.csv', 'invoices.csv', 'invoice_items.csv', 'payments.csv'];

    /**
     * ImportController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_import');
    }

    /**
     * @originalName index
     *
     * @originalFile ImportController.php
     */
    public function index($page = 0)
    {
        $this->mdl_import->paginate(site_url('import/index'), $page);
        $imports = $this->mdl_import->result();
        $this->layout->set('imports', $imports);
        $this->layout->buffer('content', 'import/index');
        $this->layout->render();
    }

    /**
     * @originalName form
     *
     * @originalFile ImportController.php
     */
    public function form()
    {
        if ( ! $this->input->post('btn_submit')) {
            $this->load->helper('directory');
            $files = directory_map('./uploads/import');
            foreach ($files as $key => $file) {
                if ( ! is_numeric(array_search($file, $this->allowed_files, true))) {
                    unset($files[$key]);
                }
            }
            $this->layout->set('files', $files);
            $this->layout->buffer('content', 'import/import_index');
            $this->layout->render();
        } else {
            $this->load->helper('file');
            $import_id = $this->mdl_import->startImport();
            if ($this->input->post('files')) {
                $files = $this->allowed_files;
                foreach ($files as $key => $file) {
                    if ( ! is_numeric(array_search($file, $this->input->post('files'), true))) {
                        unset($files[$key]);
                    }
                }
                foreach ($files as $file) {
                    switch ($file) {
                        case 'clients.csv':
                            $ids = $this->mdl_import->importData($file, 'ip_clients');
                            $this->mdl_import->recordImportDetails($import_id, 'ip_clients', 'clients', $ids);
                            break;
                        case 'invoices.csv':
                            $this->load->model('invoices/mdl_invoices');
                            $ids = $this->mdl_import->importInvoices();
                            $this->mdl_import->recordImportDetails($import_id, 'ip_invoices', 'invoices', $ids);
                            break;
                        case 'invoice_items.csv':
                            $this->load->model('invoices/mdl_items');
                            $ids = $this->mdl_import->importInvoiceItems();
                            $this->mdl_import->recordImportDetails($import_id, 'ip_invoice_items', 'invoice_items', $ids);
                            break;
                        case 'payments.csv':
                            $this->load->model('payments/mdl_payments');
                            $ids = $this->mdl_import->importPayments();
                            $this->mdl_import->recordImportDetails($import_id, 'ip_payments', 'payments', $ids);
                            break;
                    }
                }
            }
            redirect('import');
        }
    }

    /**
     * @originalName delete
     *
     * @originalFile ImportController.php
     */
    public function delete($id)
    {
        $this->mdl_import->delete($id);
        redirect('import');
    }
}
