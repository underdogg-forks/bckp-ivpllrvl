<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Customfields\Models;

use Modules\Core\Models\MyModel;
/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */
#[AllowDynamicProperties]
class CustomField extends MyModel
{
    public $table = 'ip_custom_fields';
    public $primary_key = 'ip_custom_fields.custom_field_id';
    /**
     * @originalName getNicename
     *
     * @originalFile CustomField.php
     */
    public static function getNicename($element)
    {
        if (in_array($element, self::customTypes())) {
            return mb_strtolower(str_replace('-', '', $element));
        }
        return 'fallback';
    }
    /**
     * @originalName customTypes
     *
     * @originalFile CustomField.php
     */
    public static function customTypes()
    {
        $CI =& get_instance();
        $CI->load->model('custom_values/mdl_custom_values');
        return Mdl_Custom_Values::customTypes();
    }
    /**
     * @originalName defaultSelect
     *
     * @originalFile CustomField.php
     */
    public function defaultSelect()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_custom_fields.*', false);
    }
    /**
     * @originalName defaultOrderBy
     *
     * @originalFile CustomField.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('custom_field_table ASC, custom_field_order ASC, custom_field_label ASC');
    }
    /**
     * @originalName validationRules
     *
     * @originalFile CustomField.php
     */
    public function validationRules()
    {
        return ['custom_field_table' => ['field' => 'custom_field_table', 'label' => trans('table'), 'rules' => 'required'], 'custom_field_label' => ['field' => 'custom_field_label', 'label' => trans('label'), 'rules' => 'required|max_length[50]'], 'custom_field_type' => ['field' => 'custom_field_type', 'label' => trans('type'), 'rules' => 'required'], 'custom_field_order' => ['field' => 'custom_field_order', 'label' => trans('order'), 'rules' => 'is_natural'], 'custom_field_location' => ['field' => 'custom_field_location', 'label' => trans('position'), 'rules' => 'is_natural']];
    }
    /**
     * @originalName getByTable
     *
     * @originalFile CustomField.php
     */
    public function getByTable($table)
    {
        $this->where('custom_field_table', $table);
        return $this->get()->result();
    }
    /**
     * @originalName save
     *
     * @originalFile CustomField.php
     */
    public function save($id = null, $db_array = null)
    {
        if ($id) {
            // GetController the original record before saving
            $original_record = $this->getById($id);
        }
        // Create the record
        $db_array = $db_array ? $db_array : $this->dbArray();
        // Save the record to ip_custom_fields
        $id = parent::save($id, $db_array);
        return $id;
    }
    /**
     * @originalName getPositions
     *
     * @originalFile CustomField.php
     */
    public function getPositions($table_name = false)
    {
        $this->load->model(['custom_fields/mdl_client_custom', 'custom_fields/mdl_invoice_custom', 'custom_fields/mdl_payment_custom', 'custom_fields/mdl_quote_custom', 'custom_fields/mdl_user_custom']);
        $p = $table_name ? 'ip_' : '';
        $s = $table_name ? '_custom' : '';
        $positions = [$p . 'client' . $s => Mdl_client_custom::$positions, $p . 'invoice' . $s => Mdl_invoice_custom::$positions, $p . 'payment' . $s => Mdl_payment_custom::$positions, $p . 'quote' . $s => Mdl_quote_custom::$positions, $p . 'user' . $s => Mdl_user_custom::$positions];
        foreach ($positions as $key => $val) {
            foreach ($val as $key2 => $val2) {
                $val[$key2] = trans($val2);
            }
            $positions[$key] = $val;
        }
        return $positions;
    }
    /**
     * @originalName getById
     *
     * @originalFile CustomField.php
     */
    public function getById($column)
    {
        $this->where('custom_field_id', $column);
        return $this->get()->row();
    }
    /**
     * @originalName dbArray
     *
     * @originalFile CustomField.php
     */
    public function dbArray()
    {
        // GetController the default db array
        $db_array = parent::dbArray();
        // Check if the user wants to add 'id' as custom field
        if (mb_strtolower($db_array['custom_field_label']) == 'id') {
            // Replace 'id' with 'field_id' to avoid problems with the primary key
            $custom_field_label = 'field_id';
        } else {
            $custom_field_label = mb_strtolower(str_replace(' ', '_', $db_array['custom_field_label']));
        }
        if (in_array($db_array['custom_field_type'], $this->customTypes())) {
            $type = $db_array['custom_field_type'];
        } else {
            $type = $this->customTypes()[0];
        }
        $db_array['custom_field_type'] = $type;
        // Return the db array
        return $db_array;
    }
    /**
     * @originalName customTables
     *
     * @originalFile CustomField.php
     */
    public function customTables()
    {
        return ['ip_client_custom' => 'client', 'ip_invoice_custom' => 'invoice', 'ip_payment_custom' => 'payment', 'ip_quote_custom' => 'quote', 'ip_user_custom' => 'user'];
    }
    /**
     * @originalName used
     *
     * @originalFile CustomField.php
     */
    public function used($id = null, $get = true)
    {
        if (!$id) {
            return;
        }
        $cf = $this->getById($id);
        $base = strtr($cf->custom_field_table, ['ip_' => '']) . '_field';
        $this->db->from($cf->custom_field_table)->where($base . 'id', $id)->where($base . 'value IS NOT NULL', null, false)->where($base . 'value <> ""');
        return $get ? $this->db->get()->result() : $this->db;
    }
    /**
     * @originalName delete
     *
     * @originalFile CustomField.php
     */
    public function delete($id): bool
    {
        if (!$this->used($id)) {
            $custom_field = $this->getById($id);
            // Remove MULTIPLE|SINGLE CHOICE values
            if (preg_match('/CHOICE/', $custom_field->custom_field_type)) {
                $this->load->model('custom_values/mdl_custom_values');
                $this->mdl_custom_values->deleteAllFid($id);
            }
            // Remove reference in custom table
            $base = strtr($custom_field->custom_field_table, ['ip_' => '']) . '_field';
            $this->db->from($custom_field->custom_field_table)->where($base . 'id', $id)->delete($custom_field->custom_field_table);
            parent::delete($id);
            return true;
        }
        return false;
    }
    /**
     * @originalName byTableName
     *
     * @originalFile CustomField.php
     */
    public function byTableName($name)
    {
        $table = array_flip($this->customTables());
        // get ip_*name*_custom
        $this->byTable($table[$name]);
        return $this;
    }
    /**
     * @originalName byTable
     *
     * @originalFile CustomField.php
     */
    public function byTable($table)
    {
        $this->filter_where('custom_field_table', $table);
        return $this;
    }
    /**
     * @originalName getValueForField
     *
     * @originalFile CustomField.php
     */
    public function getValueForField($field_id, $custom_field_model, $object)
    {
        $this->load->model('custom_fields/' . $custom_field_model);
        $cf_table = str_replace('mdl_', '', $custom_field_model);
        $cf_model_name = str_replace('_custom', '', $cf_table);
        $value = $this->{$custom_field_model}->where($cf_table . '_fieldid', $field_id)->where($cf_model_name . '_id', $object->{$cf_model_name . '_id'})->get()->result();
        $value_key = $cf_table . '_fieldvalue';
        $value_key_serialized = $cf_table . '_fieldvalue_serialized';
        if (!isset($value[0]->{$value_key})) {
            return '';
        }
        return is_array($value[0]->{$value_key}) ? $value[0]->{$value_key_serialized} : $value[0]->{$value_key};
    }
    /**
     * @originalName getValuesForFields
     *
     * @originalFile CustomField.php
     */
    public function getValuesForFields($custom_field_model, $model_id)
    {
        $this->load->model('custom_fields/' . $custom_field_model);
        $this->load->model('custom_values/mdl_custom_values');
        $fields = $this->{$custom_field_model}->byId($model_id)->get()->result();
        if (empty($fields)) {
            return [];
        }
        $values = [];
        $custom_field = str_replace('mdl_', '', $custom_field_model);
        foreach ($fields as $field) {
            // GetController the custom field value
            $field_id_fieldlabel = $custom_field . '_fieldvalue';
            // Check if exist !(null or '')
            if (!$field->{$field_id_fieldlabel}) {
                $values[$field->custom_field_label] = null;
                // $field->$field_id_fieldlabel
                continue;
            }
            if ($field->custom_field_type == 'MULTIPLE-CHOICE') {
                $custom_values = $this->mdl_custom_values->getByIds($field->{$field_id_fieldlabel})->result();
                if (!empty($custom_values)) {
                    $key_serialized = $field_id_fieldlabel . '_serialized';
                    $field->{$field_id_fieldlabel} = [];
                    $field->{$key_serialized} = '';
                    foreach ($custom_values as $custom_value) {
                        //Fix compatibility issue with php 5.6
                        $field->{$field_id_fieldlabel}[] = $custom_value->custom_values_value;
                        // Add as serialized string
                        $field->{$key_serialized} .= $custom_value->custom_values_value;
                        $field->{$key_serialized} .= $custom_value === end($custom_values) ? '' : ', ';
                    }
                }
            } elseif ($field->custom_field_type == 'SINGLE-CHOICE') {
                $custom_value = $this->mdl_custom_values->getById($field->{$field_id_fieldlabel})->result();
                if (!empty($custom_value)) {
                    $custom_value = $custom_value[0];
                    $field->{$field_id_fieldlabel} = $custom_value->custom_values_value;
                }
            }
            $values[$field->custom_field_label] = $field->{$field_id_fieldlabel};
        }
        return $values;
    }
    /**
     * @originalName renameColumn
     *
     * @originalFile CustomField.php
     */
    private function renameColumn($table_name, $old_column_name, $new_column_name)
    {
        $this->load->dbforge();
        $column = [$old_column_name => ['name' => $new_column_name, 'type' => 'VARCHAR', 'constraint' => 50]];
        $this->dbforge->modify_column($table_name, $column);
    }
    /**
     * @originalName addColumn
     *
     * @originalFile CustomField.php
     */
    private function addColumn($table_name, $column_name)
    {
        $this->load->dbforge();
        $column = [$column_name => ['type' => 'VARCHAR', 'constraint' => 256]];
        $this->dbforge->addColumn($table_name, $column);
    }
}
