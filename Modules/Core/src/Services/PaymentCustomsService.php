<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\DB;

use AllowDynamicProperties;
use Modules\Payments\Models\PaymentCustom;

#[AllowDynamicProperties]
class PaymentCustomsService extends BaseService
{
    public static $positions = ['custom_fields'];

    public $table = 'ip_payment_custom';

    public $primary_key = 'ip_payment_custom.payment_custom_id';

    /**
     * @originalName defaultSelect
     *
     * @originalFile PaymentCustom.php
     */
    public function defaultSelect()
    {
        DB::select('SQL_CALC_FOUND_ROWS ip_payment_custom.*, ip_custom_fields.*', false);
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile PaymentCustom.php
     */
    public function defaultJoin()
    {
        DB::join('ip_custom_fields', 'ip_payment_custom.payment_custom_fieldid = ip_custom_fields.custom_field_id');
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile PaymentCustom.php
     */
    public function defaultOrderBy()
    {
        DB::orderBy('custom_field_table ASC, custom_field_order ASC, custom_field_label ASC');
    }

    /**
     * Save custom field values for a payment after validating provided data.
     *
     * Validates the given data and, when validation passes, persists each custom field value found in the instance's `_formdata` for the specified payment. If no `_formdata` is present, the method does nothing and succeeds.
     *
     * @param int|string $payment_id the payment identifier to associate custom field values with
     * @param array      $db_array   data used for validation
     *
     * @return mixed `true` if the values were validated and saved or no form data existed; otherwise the validation result describing errors
     */
    public function saveCustom($payment_id, $db_array)
    {
        $result = $this->validate($db_array);
        if ($result === true) {
            $form_data = property_exists($this, '_formdata') && $this->_formdata !== null ? $this->_formdata : null;
            if (null === $form_data) {
                return true;
            }
            foreach ($form_data as $key => $value) {
                \Modules\Payments\Models\PaymentCustom::query()->updateOrCreate(
                    [
                        'payment_id'             => $payment_id,
                        'payment_custom_fieldid' => $key,
                    ],
                    [
                        'payment_id'                => $payment_id,
                        'payment_custom_fieldid'    => $key,
                        'payment_custom_fieldvalue' => $value,
                    ]
                );
            }

            return true;
        }

        return $result;
    }

    /**
     * @originalName byId
     *
     * @originalFile PaymentCustom.php
     */
    public function byId($payment_id)
    {
        DB::where('ip_payment_custom.payment_id', $payment_id);

        return $this;
    }

    /**
     * Retrieve custom field values for a specific payment joined with their field definitions.
     *
     * @param int $payment_id the payment identifier to filter custom fields by
     *
     * @return \Illuminate\Database\Eloquent\Collection collection of payment custom records joined with their corresponding custom field definitions, ordered by custom_field_table, custom_field_order, then custom_field_label
     */
    public function getByPayid($payment_id)
    {
        return PaymentCustom::query()
            ->select('ip_payment_custom.*', 'ip_custom_fields.*')
            ->join('ip_custom_fields', 'ip_payment_custom.payment_custom_fieldid', '=', 'ip_custom_fields.custom_field_id')
            ->where('ip_payment_custom.payment_id', $payment_id)
            ->orderBy('custom_field_table')
            ->orderBy('custom_field_order')
            ->orderBy('custom_field_label')
            ->get();
    }
}
