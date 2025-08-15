<?php

namespace Modules\CustomFields\Services;

use AllowDynamicProperties;
use App\Services\BaseService;

#[AllowDynamicProperties]
class ClientCustomService extends BaseService
{
    public static $positions = ['custom_fields', 'address', 'contact_information', 'personal_information', 'tax_information'];

    public $table = 'ip_client_custom';

    public $primary_key = 'ip_client_custom.client_custom_id';

    /**
     * @originalName defaultSelect
     *
     * @originalFile ClientCustom.php
     */
    public function defaultSelect()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_client_custom.*, ip_custom_fields.*', false);
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile ClientCustom.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('custom_field_table ASC, custom_field_order ASC, custom_field_label ASC');
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile ClientCustom.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_custom_fields', 'ip_client_custom.client_custom_fieldid = ip_custom_fields.custom_field_id', 'inner');
    }

    /**
     * @originalName saveCustom
     *
     * @originalFile ClientCustom.php
     */
    public function saveCustom($client_id, $db_array)
    {
        $result = $this->validate($db_array);
        if ($result === true) {
            $form_data = property_exists($this, '_formdata') && $this->_formdata !== null ? $this->_formdata : null;
            if (null === $form_data) {
                return true;
            }
            $client_custom_id      = null;
            $db_array['client_id'] = $client_id;
            foreach ($form_data as $key => $value) {
                $db_array      = ['client_id' => $client_id, 'client_custom_fieldid' => $key, 'client_custom_fieldvalue' => $value];
                $client_custom = $this->where('client_id', $client_id)->where('client_custom_fieldid', $key)->get();
                if ($client_custom->numRows()) {
                    $client_custom_id = $client_custom->row()->client_custom_id;
                }
                parent::save($client_custom_id, $db_array);
            }

            return true;
        }

        return $result;
    }

    /**
     * @originalName prepForm
     *
     * @originalFile ClientCustom.php
     */
    public function prepForm($id = null)
    {
        if ($id) {
            $values = $this->getByClient($id)->result();
            $this->load->helper('custom_values_helper');
            $this->load->module('custom_fields/mdl_custom_fields');
            if ($values != null) {
                foreach ($values as $value) {
                    $type = $value->custom_field_type;
                    if ($type != null) {
                        $nicename  = Mdl_Custom_Fields::getNicename($type);
                        $formatted = call_user_func('format_' . $nicename, $value->client_custom_fieldvalue);
                        $this->setFormValue('cf_' . $value->custom_field_id, $formatted);
                    }
                }
            }
            parent::prepForm($id);
        }
    }

    /**
     * @originalName getByClient
     *
     * @originalFile ClientCustom.php
     */
    public function getByClient($client_id)
    {
        $this->where('client_id', $client_id);

        return $this->get();
    }

    /**
     * @originalName byId
     *
     * @originalFile ClientCustom.php
     */
    public function byId($client_id)
    {
        $this->db->where('ip_client_custom.client_id', $client_id);

        return $this;
    }

    /**
     * @originalName getByClid
     *
     * @originalFile ClientCustom.php
     */
    public function getByClid($client_id)
    {
        return $this->where('ip_client_custom.client_id', $client_id)->get()->result();
    }

    /**
     * @originalName dbArray
     *
     * @originalFile ClientCustom.php
     */
    public function dbArray()
    {
        $db_array = parent::dbArray();
        $this->load->module('custom_fields/mdl_custom_fields');
        $fields = $this->mdl_custom_fields->result();
        foreach ($fields as $field) {
            if ($field->custom_field_type == 'DATE') {
                $db_array[$field->custom_field_column] = date_to_mysql($db_array[$field->custom_field_column]);
            } elseif ($field->custom_field_type == 'MULTIPLE-CHOICE') {
                $db_array[$field->custom_field_column] = implode(',', $db_array[$field->custom_field_column]);
            }
        }

        return $db_array;
    }
}
