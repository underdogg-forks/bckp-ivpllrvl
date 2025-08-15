<?php

namespace Modules\TaxRates\Services;

use AllowDynamicProperties;
use App\Services\BaseService;

#[AllowDynamicProperties]
class TaxRatesService extends BaseService
{
    public $table = 'ip_tax_rates';

    public $primary_key = 'ip_tax_rates.tax_rate_id';

    /**
     * @originalName defaultSelect
     *
     * @originalFile TaxRate.php
     */
    public function defaultSelect()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile TaxRate.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('ip_tax_rates.tax_rate_percent');
    }

    /**
     * @originalName validationRules
     *
     * @originalFile TaxRate.php
     */
    public function validationRules()
    {
        return ['tax_rate_name' => ['field' => 'tax_rate_name', 'label' => trans('tax_rate_name'), 'rules' => 'required'], 'tax_rate_percent' => ['field' => 'tax_rate_percent', 'label' => trans('tax_rate_percent'), 'rules' => 'required']];
    }
}
