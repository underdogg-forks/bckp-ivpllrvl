<?php

namespace src\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use src\Models\Family;

#[AllowDynamicProperties]
class FamiliesService extends BaseService
{
    public $table = 'ip_families';

    public $primary_key = 'ip_families.family_id';

    /**
     * Get a base Family query for select.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultSelect(): \Illuminate\Database\Eloquent\Builder
    {
        return Family::query();
    }

    /**
     * Get a Family query ordered by family_name.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultOrderBy(): \Illuminate\Database\Eloquent\Builder
    {
        return Family::query()->orderBy('family_name');
    }

    /**
     * Validation rules for the Family entity.
     *
     * Returns an associative array mapping field names to their validation configuration.
     *
     * @originalName validationRules
     *
     * @originalFile Family.php
     *
     * @return array Associative array of validation rules; each value contains keys `'field'`, `'label'`, and `'rules'` (e.g., `'family_name'` with a translated label and the `'required'` rule).
     */
    public function validationRules()
    {
        return ['family_name' => ['field' => 'family_name', 'label' => trans('family_name'), 'rules' => 'required']];
    }

    /**
     * Retrieve all family records.
     *
     * @return \Illuminate\Database\Eloquent\Collection a collection of Family models
     */
    public function getAll()
    {
        return Family::query()->get();
    }
}
