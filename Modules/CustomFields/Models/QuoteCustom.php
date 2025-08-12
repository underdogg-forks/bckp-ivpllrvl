<?php

namespace Modules\CustomFields\Models;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class QuoteCustom extends Validator
{
    public static $positions = ['custom_fields', 'properties'];

    public $table = 'ip_quote_custom';

    public $primary_key = 'ip_quote_custom.quote_custom_id';

    /**
     * @originalName defaultSelect
     *
     * @originalFile QuoteCustom.php
     */
    public function defaultSelect()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_quote_custom.*, ip_custom_fields.*', false);
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile QuoteCustom.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_custom_fields', 'ip_quote_custom.quote_custom_fieldid = ip_custom_fields.custom_field_id');
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile QuoteCustom.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('custom_field_table ASC, custom_field_order ASC, custom_field_label ASC');
    }

    /**
     * @originalName saveCustom
     *
     * @originalFile QuoteCustom.php
     */
    public function saveCustom($quote_id, $db_array)
    {
        $result = $this->validate($db_array);
        if ($result === true) {
            $form_data = property_exists($this, '_formdata') && $this->_formdata !== null ? $this->_formdata : null;
            if (null === $form_data) {
                return true;
            }
            $quote_custom_id = null;
            foreach ($form_data as $key => $value) {
                $db_array     = ['quote_id' => $quote_id, 'quote_custom_fieldid' => $key, 'quote_custom_fieldvalue' => $value];
                $quote_custom = $this->where('quote_id', $quote_id)->where('quote_custom_fieldid', $key)->get();
                if ($quote_custom->numRows()) {
                    $quote_custom_id = $quote_custom->row()->quote_custom_id;
                }
                parent::save($quote_custom_id, $db_array);
            }

            return true;
        }

        return $result;
    }

    /**
     * @originalName byId
     *
     * @originalFile QuoteCustom.php
     */
    public function byId($quote_id)
    {
        $this->db->where('ip_quote_custom.quote_id', $quote_id);

        return $this;
    }
}
