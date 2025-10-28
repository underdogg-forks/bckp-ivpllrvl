<?php

namespace Modules\Core\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use src\Models\TaxRate;

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
     * Create a TaxRate query builder ordered by tax_rate_percent.
     *
     * @return \Illuminate\Database\Eloquent\Builder a query builder for TaxRate records ordered by `tax_rate_percent`
     */
    public function defaultOrderBy(): \Illuminate\Database\Eloquent\Builder
    {
        return TaxRate::query()->orderBy('tax_rate_percent');
    }

    /**
     * Get validation metadata for tax rate fields.
     *
     * Maps the keys 'tax_rate_name' and 'tax_rate_percent' to an array containing
     * 'field' (field name), 'label' (translated label), and 'rules' (validation rules).
     *
     * @return array<string, array{field:string, label:string, rules:string}> mapping for 'tax_rate_name' and 'tax_rate_percent'
     */
    public function validationRules()
    {
        return ['tax_rate_name' => ['field' => 'tax_rate_name', 'label' => trans('tax_rate_name'), 'rules' => 'required'], 'tax_rate_percent' => ['field' => 'tax_rate_percent', 'label' => trans('tax_rate_percent'), 'rules' => 'required']];
    }

    /**
     * Retrieve all tax rate records.
     *
     * @return \Illuminate\Database\Eloquent\Collection<\src\Models\TaxRate> collection of TaxRate models
     */
    public function getAll()
    {
        return \src\Models\TaxRate::query()->get();
    }
}
