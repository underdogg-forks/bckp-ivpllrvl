<?php

namespace Modules\Families\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\Families\Models\Family;

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
     * @originalName validationRules
     *
     * @originalFile Family.php
     */
    public function validationRules()
    {
        return ['family_name' => ['field' => 'family_name', 'label' => trans('family_name'), 'rules' => 'required']];
    }

    /**
     * Get all families
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return \Modules\Families\Models\Family::query()->get();
    }
}
