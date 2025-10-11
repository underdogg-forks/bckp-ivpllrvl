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
     * Provide validation rules for tax rate fields.
     *
     * Returns an associative array mapping field names to their validation metadata (field, label, and rules).
     *
     * @return array<string, array{field:string, label:string, rules:string}> Validation rules for 'tax_rate_name' and 'tax_rate_percent'.
     */
    public function validationRules()
    {
        return ['tax_rate_name' => ['field' => 'tax_rate_name', 'label' => trans('tax_rate_name'), 'rules' => 'required'], 'tax_rate_percent' => ['field' => 'tax_rate_percent', 'label' => trans('tax_rate_percent'), 'rules' => 'required']];
    }

    /**
     * Retrieve all tax rate records.
     *
     * @return \Illuminate\Database\Eloquent\Collection<\Modules\TaxRates\Models\TaxRate> Collection of TaxRate models.
     */
    public function getAll()
    {
        return \Modules\TaxRates\Models\TaxRate::query()->get();
    }
}