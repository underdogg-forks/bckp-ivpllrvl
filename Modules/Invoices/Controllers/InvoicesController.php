<?php

namespace Modules\Invoices\Controllers;

use AllowDynamicProperties;
use Illuminate\Support\Facades\Log;
use Modules\Core\Controllers\AdminController;
use Modules\CustomFields\Services\CustomFieldsService;
use Modules\CustomValues\Services\CustomValuesService;
use Modules\Invoices\Services\InvoiceAmountsService;
use Modules\Invoices\Services\InvoiceCustomService;
use Modules\Invoices\Services\InvoicesService;
use Modules\Invoices\Services\InvoiceTaxRatesService;
use Modules\Invoices\Services\ItemsService;
use Modules\Payments\Services\PaymentMethodsService;
use Modules\Tasks\Services\TasksService;
use Modules\TaxRates\Services\TaxRatesService;
use Modules\Units\Services\UnitsService;

#[AllowDynamicProperties]
class InvoicesController extends AdminController
{
    public function __construct(
        public InvoicesService $invoicesService,
        public ItemsService $itemsService,
        public InvoiceTaxRatesService $invoiceTaxRatesService,
        public InvoiceAmountsService $invoiceAmountsService,
        public InvoiceCustomService $invoiceCustomService,
        public CustomFieldsService $customFieldsService,
        public CustomValuesService $customValuesService,
        public TasksService $tasksService,
        public PaymentMethodsService $paymentMethodsService,
        public UnitsService $unitsService,
        public TaxRatesService $taxRatesService
    ) {
        parent::__construct();
    }

    /**
     * @originalName index
     *
     * @originalFile InvoicesController.php
     */
    public function index(): void
    {
        // Display all invoices by default
        redirect()->route('invoices/status/all');
    }

    /**
     * @originalName status
     *
     * @originalFile InvoicesController.php
     */
    public function status(string $status = 'all', $page = 0)
    {
        // Determine which group of invoices to load
        switch ($status) {
            case 'draft':
                $this->invoicesService->isDraft();
                break;
            case 'sent':
                $this->invoicesService->isSent();
                break;
            case 'viewed':
                $this->invoicesService->isViewed();
                break;
            case 'paid':
                $this->invoicesService->isPaid();
                break;
            case 'overdue':
                $this->invoicesService->isOverdue();
                break;
        }
        $this->invoicesService->paginate(site_url('invoices/status/' . $status), $page);
        $invoices = $this->invoicesService->result();

        return view('invoices.index', ['invoices' => $invoices, 'status' => $status, 'filter_display' => true, 'filter_placeholder' => trans('filter_invoices'), 'filter_method' => 'filter_invoices', 'invoice_statuses' => $this->invoicesService->statuses()]);
    }

    /**
     * @originalName archive
     *
     * @originalFile InvoicesController.php
     */
    public function archive()
    {
        $invoice_array = $this->invoicesService->getArchives(0);

        return view('invoices.archive', ['filter_display' => true, 'filter_placeholder' => trans('filter_archives'), 'filter_method' => 'filter_archives', 'invoices_archive' => $invoice_array]);
    }

    /**
     * @originalName download
     *
     * @originalFile InvoicesController.php
     */
    public function download($invoice): void
    {
        $safeBaseDir = realpath(UPLOADS_ARCHIVE_FOLDER);
        $fileName    = urldecode(basename($invoice));
        // Strip directory traversal sequences
        $filePath = realpath($safeBaseDir . DIRECTORY_SEPARATOR . $fileName);
        if ($filePath === false || ! str_starts_with($filePath, $safeBaseDir)) {
            Log::error('Invalid file access attempt: ' . $fileName);
            show_404();

            return;
        }
        if ( ! file_exists($filePath)) {
            Log::error('While downloading: File not found: ' . $filePath);
            show_404();

            return;
        }
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    /**
     * @originalName view
     *
     * @originalFile InvoicesController.php
     */
    public function view($invoice_id): void
    {
        $fields  = $this->invoiceCustomService->byId($invoice_id)->get()->result();
        $invoice = $this->invoicesService->getById($invoice_id);
        if ( ! $invoice) {
            abort(404);
        }
        $custom_fields = $this->customFieldsService->byTable('ip_invoice_custom')->get()->result();
        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, $this->customValuesService->customValueFields())) {
                $values                                        = $this->customValuesService->getByFid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }
        foreach ($custom_fields as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->invoice_custom_fieldid == $cfield->custom_field_id) {
                    $this->invoicesService->setFormValue('custom[' . $cfield->custom_field_id . ']', $fvalue->invoice_custom_fieldvalue);
                    break;
                }
            }
        }
        $payment_cf       = $this->customFieldsService->byTable('ip_payment_custom')->get();
        $payment_cf_exist = $payment_cf->numRows() > 0 ? 'yes' : 'no';
        $items = $this->itemsService->where('invoice_id', $invoice_id)->get()->result();
        $einvoice = get_einvoice_usage($invoice, $items);
        $change_user = $this->invoicesService->getChangeUser();
        return view('invoices.view' . ($invoice->sumex_id ? '_sumex' : ''), [
            'invoice' => $invoice,
            'items' => $items,
            'invoice_id' => $invoice_id,
            'einvoice' => $einvoice,
            'change_user' => $change_user,
            'tax_rates' => $this->taxRatesService->get()->result(),
            'invoice_tax_rates' => $this->invoiceTaxRatesService->where('invoice_id', $invoice_id)->get()->result(),
            'units' => $this->unitsService->get()->result(),
            'payment_methods' => $this->paymentMethodsService->get()->result(),
            'custom_fields' => $custom_fields,
            'custom_values' => $custom_values,
            'custom_js_vars' => [
                'currency_symbol' => get_setting('currency_symbol'),
                'currency_symbol_placement' => get_setting('currency_symbol_placement'),
                'decimal_point' => get_setting('decimal_point'),
            ],
            'invoice_statuses' => $this->invoicesService->statuses(),
            'payment_cf_exist' => $payment_cf_exist,
            'legacy_calculation' => config_item('legacy_calculation'),
        ]);
    }

    /**
     * @originalName delete
     *
     * @originalFile InvoicesController.php
     */
    public function delete($invoice_id): void
    {
        $invoice        = $this->invoicesService->getById($invoice_id);
        $invoice_status = $invoice->invoice_status_id;
        if ($invoice_status == 1 || $this->config->item('enable_invoice_deletion') === true) {
            $this->tasksService->updateOnInvoiceDelete($invoice_id);
            $this->invoicesService->delete($invoice_id);
        } else {
            session()->flash('alert_error', trans('invoice_deletion_forbidden'));
        }
        return redirect()->route('invoices.index');
    }

    /**
     * @originalName generatePdf
     *
     * @originalFile InvoicesController.php
     */
    public function generatePdf($invoice_id, $stream = true, $invoice_template = null): void
    {
        if (get_setting('mark_invoices_sent_pdf') == 1) {
            $this->invoicesService->generateInvoiceNumberIfApplicable($invoice_id);
            $this->invoicesService->markSent($invoice_id);
        }
        generate_invoice_pdf($invoice_id, $stream, $invoice_template, null);
    }

    /**
     * @originalName generateXml
     *
     * @originalFile InvoicesController.php
     */
    public function generateXml($invoice_id): void
    {
        $invoice = $this->invoicesService->getById($invoice_id);
        if ( ! $invoice) {
            abort(404);
        }
        $items = $this->itemsService->where('invoice_id', $invoice_id)->get()->result();
        $einvoice = get_einvoice_usage($invoice, $items, false);
        if ( ! $einvoice->user) {
            abort(404);
        }
        $xml_id = $einvoice->name;
        $options   = [];
        $generator = $xml_id;
        $path      = app_path('Helpers/XMLconfigs/');
        if ($xml_id && file_exists($path . $xml_id . '.php') && include $path . $xml_id . '.php') {
            $embed_xml = $xml_setting['embedXML'];
            $XMLname   = $xml_setting['XMLname'];
            $options   = empty($xml_setting['options']) ? $options : $xml_setting['options'];
            $generator = empty($xml_setting['generator']) ? $generator : $xml_setting['generator'];
        }
        $filename = trans('invoice') . '_' . str_replace(['\\', '/'], '_', $invoice->invoice_number);
        $path     = generate_xml_invoice_file($invoice, $items, $generator, $filename, $options);
        response()->header('Content-Type', 'text/xml')->setContent(file_get_contents($path));
        unlink($path);
    }

    /**
     * @originalName generateSumexPdf
     *
     * @originalFile InvoicesController.php
     */
    public function generateSumexPdf($invoice_id): void
    {
        generate_invoice_sumex($invoice_id);
    }

    /**
     * @originalName generateSumexCopy
     *
     * @originalFile InvoicesController.php
     */
    public function generateSumexCopy($invoice_id): void
    {
        $sumex = new \Modules\Core\Libraries\Sumex([
            'invoice' => $this->invoicesService->getById($invoice_id),
            'items' => $this->itemsService->where('invoice_id', $invoice_id)->get()->result(),
            'options' => ['copy' => '1', 'storno' => '0'],
        ]);
        response()->header('Content-Type', 'application/pdf')->setContent($sumex->pdf());
    }

    /**
     * @originalName deleteInvoiceTax
     *
     * @originalFile InvoicesController.php
     */
    public function deleteInvoiceTax(string $invoice_id, $invoice_tax_rate_id): void
    {
        $this->invoiceTaxRatesService->delete($invoice_tax_rate_id);
        $global_discount['item'] = $this->invoiceAmountsService->getGlobalDiscount($invoice_id);
        $this->invoiceAmountsService->calculate($invoice_id, $global_discount);
        return redirect('invoices/view/' . $invoice_id);
    }

    /**
     * @originalName recalculateAllInvoices
     *
     * @originalFile InvoicesController.php
     */
    public function recalculateAllInvoices(): void
    {
        $invoice_ids = $this->db->table('ip_invoices')->pluck('invoice_id');
        foreach ($invoice_ids as $invoice_id) {
            $global_discount['item'] = $this->invoiceAmountsService->getGlobalDiscount($invoice_id);
            $this->invoiceAmountsService->calculate($invoice_id, $global_discount);
        }
    }
}
