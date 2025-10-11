<?php

namespace Modules\Units\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\Units\Models\Unit;
use RuntimeException;

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
     * Selects the singular or plural label for a unit identified by ID based on the provided quantity.
     *
     * @param int $unit_id  the unit's primary identifier
     * @param int $quantity the quantity used to decide form; values less than -1 or greater than 1 use the plural form
     *
     * @return string|null the unit's name in the appropriate form, or `null` if no unit exists with the given ID
     */
    public function getName(int $unit_id, int $quantity): ?string
    {
        $unit = Unit::query()->find($unit_id);
        if ( ! $unit) {
            return null;
        }
        if ($quantity < -1 || $quantity > 1) {
            return $unit->unit_name_plrl;
        }

        return $unit->unit_name;
    }

    /**
     * Provide validation rules for unit fields.
     *
     * Returns an associative array keyed by field name where each entry contains
     * 'field' (form field name), 'label' (translation key for the field label),
     * and 'rules' (validation rules string).
     *
     * @return array<string, array{field:string,label:string,rules:string}>
     */
    public function validationRules()
    {
        return ['unit_name' => ['field' => 'unit_name', 'label' => trans('unit_name'), 'rules' => 'required'], 'unit_name_plrl' => ['field' => 'unit_name_plrl', 'label' => trans('unit_name_plrl'), 'rules' => 'required']];
    }

    /**
     * Retrieve all Unit records.
     *
     * @return \Illuminate\Database\Eloquent\Collection<\Modules\Units\Models\Unit> collection of Unit models
     */
    public function getAll()
    {
        return \Modules\Units\Models\Unit::query()->get();
    }

    /**
     * Check if a unit exists by name.
     *
     * @param string $unit_name
     *
     * @return bool
     */
    public function exists(string $unit_name): bool
    {
        return Unit::query()->where('unit_name', $unit_name)->exists();
    }

    /**
     * Create or update a unit.
     *
     * @param array    $data
     * @param int|null $id
     *
     * @return Unit
     */
    public function save(array $data, ?int $id = null): Unit
    {
        if (empty($id)) {
            return Unit::create($data);
        }

        $unit = Unit::find($id);
        if ( ! $unit) {
            throw new RuntimeException('Unit not found');
        }
        $unit->update($data);

        return $unit;
    }

    /**
     * Delete a unit by ID.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool
    {
        $unit = Unit::find($id);
        if ($unit) {
            return $unit->delete();
        }

        return false;
    }
}
