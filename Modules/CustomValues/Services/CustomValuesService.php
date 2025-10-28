<?php

namespace Modules\CustomValues\Services;

use AllowDynamicProperties;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\BaseService;
use Modules\CustomFields\Models\CustomField;
use Modules\CustomValues\Models\CustomValue;

#[AllowDynamicProperties]
class CustomValuesService extends BaseService
{
    public $table = 'ip_custom_values';

    public $primary_key = 'ip_custom_values.custom_values_id';

    /**
     * @originalName customTypes
     *
     * @originalFile CustomValue.php
     */
    public static function customTypes()
    {
        return array_merge(self::userInputTypes(), self::customValueFields());
    }

    /**
     * @originalName userInputTypes
     *
     * @originalFile CustomValue.php
     */
    public static function userInputTypes()
    {
        return ['TEXT', 'DATE', 'BOOLEAN'];
    }

    /**
     * @originalName customValueFields
     *
     * @originalFile CustomValue.php
     */
    public static function customValueFields()
    {
        return ['SINGLE-CHOICE', 'MULTIPLE-CHOICE'];
    }

    /**
     * @originalName saveCustom
     *
     * @originalFile CustomValue.php
     */
    public function saveCustom(int $fid): void
    {
        $fieldCustom = CustomField::query()->find($fid);
        if ( ! $fieldCustom) {
            return;
        }
        $dbArray                        = $this->dbArray();
        $dbArray['custom_values_field'] = $fid;
        CustomValue::query()->create($dbArray);
    }

    /**
     * @originalName validationRules
     *
     * @originalFile CustomValue.php
     */
    public function validationRules()
    {
        return ['custom_values_value' => ['field' => 'custom_values_value', 'label' => 'Value', 'rules' => 'required']];
    }

    /**
     * @originalName customTables
     *
     * @originalFile CustomValue.php
     */
    public function customTables()
    {
        return ['ip_client_custom' => 'client', 'ip_invoice_custom' => 'invoice', 'ip_payment_custom' => 'payment', 'ip_quote_custom' => 'quote', 'ip_user_custom' => 'user'];
    }

    /**
     * @originalName used
     *
     * @originalFile CustomValue.php
     */
    public function used(?int $id): bool
    {
        if ( ! $id) {
            return false;
        }
        $customValue = CustomValue::query()->find($id);
        if ( ! $customValue) {
            return false;
        }
        $customField = CustomField::query()->find($customValue->custom_values_field);
        if ( ! $customField) {
            return false;
        }
        $table      = $customField->custom_field_table;
        $type       = $customField->custom_field_type;
        $base       = strtr($table, ['ip_' => '']) . '_fieldvalue';
        $modelClass = $this->getModelClassForTable($table);
        if ( ! $modelClass) {
            return false;
        }
        if ($type === 'SINGLE-CHOICE') {
            return $modelClass::query()->where($base, $id)->exists();
        }

        return $modelClass::query()->where($base, 'LIKE', "%{$id},%")
            ->orWhere($base, 'LIKE', "%,{$id}%")
            ->orWhere($base, $id)
            ->exists();
    }

    /**
     * Delete a custom value if not used, log orphan handling.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete($id): bool
    {
        if ( ! $this->used($id)) {
            CustomValue::query()->where('custom_values_id', $id)->delete();
            Log::info('Orphan custom value deleted', ['custom_values_id' => $id]);

            return true;
        }

        return false;
    }

    /**
     * Delete all custom values for a field using Eloquent.
     *
     * @param int $id
     *
     * @return void
     */
    public function deleteAllFid(int $id): void
    {
        CustomValue::query()->where('custom_values_field', $id)->delete();
        Log::info('All custom values for field deleted', ['custom_values_field' => $id]);
    }

    /**
     * Get custom values by field id using Eloquent.
     *
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByFid(int $id)
    {
        return CustomValue::query()->where('custom_values_field', $id)->get();
    }

    /**
     * Get custom values by column using Eloquent.
     *
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByColumn(int $id)
    {
        return CustomValue::query()->where('custom_field_id', $id)->get();
    }

    /**
     * Get custom value by id using Eloquent.
     *
     * @param int $id
     *
     * @return CustomValue|null
     */
    public function getById(int $id): ?CustomValue
    {
        return CustomValue::query()->find($id);
    }

    /**
     * Get custom values by multiple ids using Eloquent.
     *
     * @param array|int|string $ids
     *
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function getByIds($ids)
    {
        if (empty($ids)) {
            return;
        }
        $ids = is_array($ids) ? $ids : explode(',', $ids);

        return CustomValue::query()->whereIn('custom_values_id', $ids)->get();
    }

    /**
     * Check if a column has a value using Eloquent.
     *
     * @param int $fid
     * @param int $id
     *
     * @return bool
     */
    public function columnHasValue(int $fid, int $id): bool
    {
        return CustomValue::query()
            ->where('custom_field_id', $fid)
            ->where('custom_values_id', $id)
            ->exists();
    }

    /**
     * Get grouped custom values using Eloquent.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function grouped()
    {
        return CustomValue::query()->with('customField')->get();
    }

    /**
     * @originalName defaultSelect
     *
     * @originalFile CustomValue.php
     */
    public function defaultSelect()
    {
        $this->db->select('ip_custom_fields.*,ip_custom_values.*', false);
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile CustomValue.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_custom_fields', 'ip_custom_values.custom_values_field = ip_custom_fields.custom_field_id', 'inner');
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile CustomValue.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('ip_custom_values.custom_values_value');
    }

    /**
     * @originalName defaultGroupBy
     *
     * @originalFile CustomValue.php
     */
    public function defaultGroupBy()
    {
        //$this->db->group_by('ip_custom_values.custom_values_field');
    }

    /**
     * Helper to get model class for a table name.
     *
     * @param string $table
     *
     * @return string|null
     */
    protected function getModelClassForTable(string $table): ?string
    {
        $map = [
            'ip_client_custom'  => \Modules\Crm\app\Models\ClientCustom::class,
            'ip_invoice_custom' => \Modules\Invoices\Models\InvoiceCustom::class,
            'ip_payment_custom' => \Modules\Payments\Models\PaymentCustom::class,
            'ip_quote_custom'   => \Modules\Quotes\Models\QuoteCustom::class,
            'ip_user_custom'    => \Modules\Users\Models\UserCustom::class,
        ];

        return $map[$table] ?? null;
    }
}
