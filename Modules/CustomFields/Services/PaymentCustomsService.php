<?php

namespace Modules\CustomFields\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\CustomFields\Models\PaymentCustom;

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
        $this->db->select('SQL_CALC_FOUND_ROWS ip_payment_custom.*, ip_custom_fields.*', false);
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile PaymentCustom.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_custom_fields', 'ip_payment_custom.payment_custom_fieldid = ip_custom_fields.custom_field_id');
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile PaymentCustom.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('custom_field_table ASC, custom_field_order ASC, custom_field_label ASC');
    }

    /**
     * @originalName saveCustom
     *
     * @originalFile PaymentCustom.php
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
                        'payment_id' => $payment_id,
                        'payment_custom_fieldid' => $key
                    ],
                    [
                        'payment_id' => $payment_id,
                        'payment_custom_fieldid' => $key,
                        'payment_custom_fieldvalue' => $value
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
        $this->db->where('ip_payment_custom.payment_id', $payment_id);

        return $this;
    }

    /**
     * @originalName getByPayid
     *
     * @originalFile PaymentCustom.php
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
