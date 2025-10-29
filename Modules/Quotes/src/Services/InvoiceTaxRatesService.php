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
     * @param InvoiceAmountsService $invoiceAmountsService service used to retrieve and recalculate invoice amounts and discounts
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
     * @param int|null   $id       the invoice tax rate identifier, or null to create a new record
     * @param array|null $db_array Associative array of database fields for the invoice tax rate. If it contains
     *                             an 'invoice_id' key that ID will be used to trigger recalculation.
     */
    public function save($id = null, $db_array = null)
    {
        // Only appliable in legacy calculation - since 1.6.3
        config_item('legacy_calculation') && parent::save($id, $db_array);
        $invoice_id = $db_array['invoice_id'] ?? request()->input('invoice_id');
        if ($invoice_id) {
            $global_discount['item'] = $this->invoiceAmountsService->getGlobalDiscount($invoice_id);
            // Recalculate invoice amounts
            $this->invoiceAmountsService->calculate($invoice_id, $global_discount);
        }
    }

    /**
     * Get validation rules for invoice tax rate records.
     *
     * Each entry contains the input field name (`field`), a translatable `label`, and the validation `rules`.
     *
     * @return array<string, array<string, string>> map of field names to their validation metadata
     */
    public function validationRules()
    {
        return [
            'invoice_id' => ['field' => 'invoice_id', 'label' => trans('invoice'), 'rules' => 'required'], 'tax_rate_id' => ['field' => 'tax_rate_id', 'label' => trans('tax_rate'), 'rules' => 'required'], 'include_item_tax' => ['field' => 'include_item_tax', 'label' => trans('tax_rate_placement'), 'rules' => 'required'],
        ];
    }

    /**
     * Retrieve all tax rate records associated with a specific invoice.
     *
     * @param int $invoice_id ID of the invoice to retrieve tax rates for
     *
     * @return \Illuminate\Database\Eloquent\Collection collection of InvoiceTaxRate models for the given invoice
     */
    /**
     * Retrieve all tax rate records for a given invoice.
     *
     * @param int $invoice_id the invoice identifier to filter tax rates by
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Modules\Invoices\Models\InvoiceTaxRate[] collection of InvoiceTaxRate models matching the invoice
     */
    public function getByInvoiceId($invoice_id)
    {
        return \Modules\Invoices\Models\InvoiceTaxRate::query()->where('invoice_id', $invoice_id)->get();
    }
}
