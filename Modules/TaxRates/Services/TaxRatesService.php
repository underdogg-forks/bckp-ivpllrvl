<?php

namespace Modules\TaxRates\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\TaxRates\Models\TaxRate;

#[AllowDynamicProperties]
class TaxRatesService extends BaseService
{
    public $table = 'ip_tax_rates';

    public $primary_key = 'ip_tax_rates.tax_rate_id';

    /**
     * Get a base TaxRate query for select.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultSelect(): \Illuminate\Database\Eloquent\Builder
    {
        return TaxRate::query();
    }

    /**
     * Get a TaxRate query ordered by tax_rate_percent.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultOrderBy(): \Illuminate\Database\Eloquent\Builder
    {
        return TaxRate::query()->orderBy('tax_rate_percent');
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
