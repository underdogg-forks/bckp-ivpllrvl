<?php

namespace Modules\CustomFields\Models;

use AllowDynamicProperties;
use Modules\Core\Validators\Validator;

#[AllowDynamicProperties]
class PaymentCustom extends Validator
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
            $payment_custom_id = null;
            foreach ($form_data as $key => $value) {
                $db_array       = ['payment_id' => $payment_id, 'payment_custom_fieldid' => $key, 'payment_custom_fieldvalue' => $value];
                $payment_custom = $this->where('payment_id', $payment_id)->where('payment_custom_fieldid', $key)->get();
                if ($payment_custom->numRows()) {
                    $payment_custom_id = $payment_custom->row()->payment_custom_id;
                }
                parent::save($payment_custom_id, $db_array);
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
        return $this->where('ip_payment_custom.payment_id', $payment_id)->get()->result();
    }
}
