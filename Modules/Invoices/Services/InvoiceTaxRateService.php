<?php

namespace Modules\Invoices\Services;

use AllowDynamicProperties;
use App\Services\BaseService;

#[AllowDynamicProperties]
class InvoiceTaxRateService extends BaseService
{
    public $table = 'ip_invoice_tax_rates';

    public $primary_key = 'ip_invoice_tax_rates.invoice_tax_rate_id';

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
     * @originalName save
     *
     * @originalFile InvoiceTaxRate.php
     */
    public function save($id = null, $db_array = null)
    {
        // Only appliable in legacy calculation - since 1.6.3
        config_item('legacy_calculation') && parent::save($id, $db_array);
        $this->load->model('invoices/mdl_invoice_amounts');
        $invoice_id = $db_array['invoice_id'] ?? $this->input->post('invoice_id');
        if ($invoice_id) {
            $global_discount['item'] = $this->mdl_invoice_amounts->getGlobalDiscount($invoice_id);
            // Recalculate invoice amounts
            $this->mdl_invoice_amounts->calculate($invoice_id, $global_discount);
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
