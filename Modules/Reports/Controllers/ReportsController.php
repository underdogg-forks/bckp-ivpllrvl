<?php

namespace Modules\Reports\Controllers;

use Illuminate\Support\Facades\Log;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class ReportsController extends AdminController
{
    /**
     * ReportsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_reports');
    }

    /**
     * @originalName salesByClient
     *
     * @originalFile ReportsController.php
     */
    public function salesByClient()
    {
        if ($this->input->post('btn_submit')) {
            $data = ['results' => (new ReportsService())->salesByClient($this->input->post('from_date'), $this->input->post('to_date')), 'from_date' => $this->input->post('from_date'), 'to_date' => $this->input->post('to_date')];
            $html = $this->load->view('reports/sales_by_client', $data, true);
            $this->load->helper('mpdf');
            pdf_create($html, trans('sales_by_client'), true);
        }
        $this->layout->buffer('content', 'reports/sales_by_client_index')->render();
    }

    /**
     * @originalName invoicesPerClient
     *
     * @originalFile ReportsController.php
     */
    public function invoicesPerClient()
    {
        if ($this->input->post('btn_submit')) {
            $data = ['results' => (new ReportsService())->invoicesPerClient($this->input->post('from_date'), $this->input->post('to_date')), 'from_date' => $this->input->post('from_date'), 'to_date' => $this->input->post('to_date')];
            $html = $this->load->view('reports/invoices_per_client', $data, true);
            $this->load->helper('mpdf');
            pdf_create($html, trans('invoices_per_client'), true);
        }
        $this->layout->buffer('content', 'reports/invoices_per_client_index')->render();
    }

    /**
     * @originalName paymentHistory
     *
     * @originalFile ReportsController.php
     */
    public function paymentHistory()
    {
        if ($this->input->post('btn_submit')) {
            $data = ['results' => (new ReportsService())->paymentHistory($this->input->post('from_date'), $this->input->post('to_date')), 'from_date' => $this->input->post('from_date'), 'to_date' => $this->input->post('to_date')];
            $html = $this->load->view('reports/payment_history', $data, true);
            $this->load->helper('mpdf');
            pdf_create($html, trans('payment_history'), true);
        }
        $this->layout->buffer('content', 'reports/payment_history_index')->render();
    }

    /**
     * @originalName invoiceAging
     *
     * @originalFile ReportsController.php
     */
    public function invoiceAging()
    {
        if ($this->input->post('btn_submit')) {
            $data = ['results' => (new ReportsService())->invoiceAging()];
            $html = $this->load->view('reports/invoice_aging', $data, true);
            $this->load->helper('mpdf');
            pdf_create($html, trans('invoice_aging'), true);
        }
        $this->layout->buffer('content', 'reports/invoice_aging_index')->render();
    }

    /**
     * @originalName salesByYear
     *
     * @originalFile ReportsController.php
     */
    public function salesByYear()
    {
        if ($this->input->post('btn_submit')) {
            $data = ['results' => (new ReportsService())->salesByYear($this->input->post('from_date'), $this->input->post('to_date'), $this->input->post('minQuantity'), $this->input->post('maxQuantity'), $this->input->post('checkboxTax')), 'from_date' => $this->input->post('from_date'), 'to_date' => $this->input->post('to_date')];
            $html = $this->load->view('reports/sales_by_year', $data, true);
            $this->load->helper('mpdf');
            pdf_create($html, trans('sales_by_date'), true);
        }
        $this->layout->buffer('content', 'reports/sales_by_year_index')->render();
    }
}
