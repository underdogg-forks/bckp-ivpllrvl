<?php

namespace Modules\Units\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\Units\Models\Unit;

#[AllowDynamicProperties]
class UnitsService extends BaseService
{
    public $table = 'ip_units';

    public $primary_key = 'ip_units.unit_id';

    /**
     * Get a base Unit query for select.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultSelect(): \Illuminate\Database\Eloquent\Builder
    {
        return Unit::query();
    }

    /**
     * Get a Unit query ordered by unit_name.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultOrderBy(): \Illuminate\Database\Eloquent\Builder
    {
        return Unit::query()->orderBy('unit_name');
    }

    /**
     * Get the name or plural name for a unit based on quantity using Eloquent.
     *
     * @param int $unit_id
     * @param int $quantity
     * @return string|null
     */
    public function getName(int $unit_id, int $quantity): ?string
    {
        $unit = Unit::query()->find($unit_id);
        if (! $unit) {
            return null;
        }
        if ($quantity < -1 || $quantity > 1) {
            return $unit->unit_name_plrl;
        }
        return $unit->unit_name;
    }

    /**
     * @originalName validationRules
     *
     * @originalFile Unit.php
     */
    public function validationRules()
    {
        return ['unit_name' => ['field' => 'unit_name', 'label' => trans('unit_name'), 'rules' => 'required'], 'unit_name_plrl' => ['field' => 'unit_name_plrl', 'label' => trans('unit_name_plrl'), 'rules' => 'required']];
    }

    /**
     * Get all units
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return \Modules\Units\Models\Unit::query()->get();
    }
}
