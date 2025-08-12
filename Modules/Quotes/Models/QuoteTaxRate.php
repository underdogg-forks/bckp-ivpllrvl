<?php

namespace Modules\Quotes\Models;

use AllowDynamicProperties;
use Modules\Core\Models\ResponseModel;

#[AllowDynamicProperties]
class QuoteTaxRate extends ResponseModel
{
    public $table = 'ip_quote_tax_rates';

    public $primary_key = 'ip_quote_tax_rates.quote_tax_rate_id';

    /**
     * @originalName defaultSelect
     *
     * @originalFile QuoteTaxRate.php
     */
    public function defaultSelect()
    {
        $this->db->select('ip_tax_rates.tax_rate_name AS quote_tax_rate_name');
        $this->db->select('ip_tax_rates.tax_rate_percent AS quote_tax_rate_percent');
        $this->db->select('ip_quote_tax_rates.*');
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile QuoteTaxRate.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_tax_rates', 'ip_tax_rates.tax_rate_id = ip_quote_tax_rates.tax_rate_id');
    }

    /**
     * @originalName save
     *
     * @originalFile QuoteTaxRate.php
     */
    public function save($id = null, $db_array = null)
    {
        // Only appliable in legacy calculation - since 1.6.3
        config_item('legacy_calculation') && parent::save($id, $db_array);
        $this->load->model('quotes/mdl_quote_amounts');
        $quote_id = $db_array['quote_id'] ?? $this->input->post('quote_id');
        if ($quote_id) {
            $global_discount['item'] = $this->mdl_quote_amounts->getGlobalDiscount($quote_id);
            // Recalculate quote amounts
            $this->mdl_quote_amounts->calculate($quote_id, $global_discount);
        }
    }

    /**
     * @originalName validationRules
     *
     * @originalFile QuoteTaxRate.php
     */
    public function validationRules()
    {
        return ['quote_id' => ['field' => 'quote_id', 'label' => trans('quote'), 'rules' => 'required'], 'tax_rate_id' => ['field' => 'tax_rate_id', 'label' => trans('tax_rate'), 'rules' => 'required'], 'include_item_tax' => ['field' => 'include_item_tax', 'label' => trans('tax_rate_placement'), 'rules' => 'required']];
    }
}
