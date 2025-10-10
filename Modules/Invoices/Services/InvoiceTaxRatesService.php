<?php

namespace Modules\Invoices\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;

#[AllowDynamicProperties]
class InvoiceTaxRatesService extends BaseService
{
    public $table = 'ip_invoice_tax_rates';

    public $primary_key = 'ip_invoice_tax_rates.invoice_tax_rate_id';

    /**
     * Create a new InvoiceTaxRatesService and inject the InvoiceAmountsService dependency.
     *
     * @param InvoiceAmountsService $invoiceAmountsService Service used to retrieve and recalculate invoice amounts and discounts.
     */
    public function __construct(public InvoiceAmountsService $invoiceAmountsService)
    {
        parent::__construct();
    }

    /**
     * @originalName defaultSelect
     *
     * @originalFile InvoiceTaxRate.php
     */
    public function defaultSelect()
    {
        $this->db->select('ip_tax_rates.tax_rate_name AS invoice_tax_rate_name');
        $this->db->select('ip_tax_rates.tax_rate_percent AS invoice_tax_rate_percent');
        $this->db->select('ip_invoice_tax_rates.*');
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile InvoiceTaxRate.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_tax_rates', 'ip_tax_rates.tax_rate_id = ip_invoice_tax_rates.tax_rate_id');
    }

    /**
     * Save an invoice tax rate and trigger invoice amount recalculation when applicable.
     *
     * If the application is in legacy calculation mode, this will also invoke the parent save implementation.
     * When an invoice identifier is available (from $db_array['invoice_id'] or POST data), the service will
     * retrieve the invoice's global discount and recalculate invoice amounts via the injected InvoiceAmountsService.
     *
     * @param int|null $id The invoice tax rate identifier, or null to create a new record.
     * @param array|null $db_array Associative array of database fields for the invoice tax rate. If it contains
     *                             an 'invoice_id' key that ID will be used to trigger recalculation.
     */
    public function save($id = null, $db_array = null)
    {
        // Only appliable in legacy calculation - since 1.6.3
        config_item('legacy_calculation') && parent::save($id, $db_array);
        $invoice_id = $db_array['invoice_id'] ?? $this->input->post('invoice_id');
        if ($invoice_id) {
            $global_discount['item'] = $this->invoiceAmountsService->getGlobalDiscount($invoice_id);
            // Recalculate invoice amounts
            $this->invoiceAmountsService->calculate($invoice_id, $global_discount);
        }
    }

    /**
     * @originalName validationRules
     *
     * @originalFile InvoiceTaxRate.php
     */
    public function validationRules()
    {
        return [
            'invoice_id' => ['field' => 'invoice_id', 'label' => trans('invoice'), 'rules' => 'required'], 'tax_rate_id' => ['field' => 'tax_rate_id', 'label' => trans('tax_rate'), 'rules' => 'required'], 'include_item_tax' => ['field' => 'include_item_tax', 'label' => trans('tax_rate_placement'), 'rules' => 'required'],
        ];
    }
}