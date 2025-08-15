<?php

namespace Modules\Import\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\Import\Services\ImportService;

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
        (new ImportService())->paginate(site_url('import/index'), $page);
        $imports = (new ImportService())->result();
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
            $import_id = (new ImportService())->startImport();
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
                            $ids = (new ImportService())->importData($file, 'ip_clients');
                            (new ImportService())->recordImportDetails($import_id, 'ip_clients', 'clients', $ids);
                            break;
                        case 'invoices.csv':
                            $this->load->model('invoices/mdl_invoices');
                            $ids = (new ImportService())->importInvoices();
                            (new ImportService())->recordImportDetails($import_id, 'ip_invoices', 'invoices', $ids);
                            break;
                        case 'invoice_items.csv':
                            $this->load->model('invoices/mdl_items');
                            $ids = (new ImportService())->importInvoiceItems();
                            (new ImportService())->recordImportDetails($import_id, 'ip_invoice_items', 'invoice_items', $ids);
                            break;
                        case 'payments.csv':
                            $this->load->model('payments/mdl_payments');
                            $ids = (new ImportService())->importPayments();
                            (new ImportService())->recordImportDetails($import_id, 'ip_payments', 'payments', $ids);
                            break;
                    }
                }
            }
            redirect()->route('import');
        }
    }

    /**
     * @originalName delete
     *
     * @originalFile ImportController.php
     */
    public function delete($id)
    {
        (new ImportService())->delete($id);
        redirect()->route('import');
    }
}
