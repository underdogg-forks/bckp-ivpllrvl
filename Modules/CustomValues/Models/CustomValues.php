<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Customvalues\Models;

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
class CustomValues extends MyModel
{
    public $table = 'ip_custom_values';
    public $primary_key = 'ip_custom_values.custom_values_id';
    /**
     * @originalName customTypes
     *
     * @originalFile CustomValues.php
     */
    public static function customTypes()
    {
        return array_merge(self::userInputTypes(), self::customValueFields());
    }
    /**
     * @originalName userInputTypes
     *
     * @originalFile CustomValues.php
     */
    public static function userInputTypes()
    {
        return ['TEXT', 'DATE', 'BOOLEAN'];
    }
    /**
     * @originalName customValueFields
     *
     * @originalFile CustomValues.php
     */
    public static function customValueFields()
    {
        return ['SINGLE-CHOICE', 'MULTIPLE-CHOICE'];
    }
    /**
     * @originalName saveCustom
     *
     * @originalFile CustomValues.php
     */
    public function saveCustom($fid)
    {
        $this->load->module('custom_fields');
        $field_custom = $this->mdl_custom_fields->getById($fid);
        if (!$field_custom) {
            return;
        }
        $db_array = $this->dbArray();
        $db_array['custom_values_field'] = $fid;
        parent::save(null, $db_array);
    }
    /**
     * @originalName validationRules
     *
     * @originalFile CustomValues.php
     */
    public function validationRules()
    {
        return ['custom_values_value' => ['field' => 'custom_values_value', 'label' => 'Value', 'rules' => 'required']];
    }
    /**
     * @originalName customTables
     *
     * @originalFile CustomValues.php
     */
    public function customTables()
    {
        return ['ip_client_custom' => 'client', 'ip_invoice_custom' => 'invoice', 'ip_payment_custom' => 'payment', 'ip_quote_custom' => 'quote', 'ip_user_custom' => 'user'];
    }
    /**
     * @originalName used
     *
     * @originalFile CustomValues.php
     */
    public function used($id = null, $get = true)
    {
        if (!$id) {
            return;
        }
        $this->load->model('custom_fields/mdl_custom_fields');
        $cv = $this->getById($id)->row();
        $cf = $this->mdl_custom_fields->getById($cv->custom_values_field);
        unset($cv);
        $base = strtr($cf->custom_field_table, ['ip_' => '']) . '_fieldvalue';
        // GetController values [SINGLE|MULTIPLE]-CHOICE
        $this->db->from($cf->custom_field_table);
        if ('SINGLE-CHOICE' == $cf->custom_field_type) {
            $this->db->where($base, $id);
        } else {
            $this->db->or_like($base, $id . ',')->or_like($base, ',' . $id)->or_where($base, $id);
        }
        return $get ? $this->db->get()->result() : $this->db;
    }
    /**
     * @originalName delete
     *
     * @originalFile CustomValues.php
     */
    public function delete($id): bool
    {
        if (!$this->used($id)) {
            parent::delete($id);
            return true;
        }
        return false;
    }
    /**
     * @originalName deleteAllFid
     *
     * @originalFile CustomValues.php
     */
    public function deleteAllFid($id)
    {
        $this->db->where('custom_values_field', $id)->delete($this->table);
    }
    /**
     * @originalName getByFid
     *
     * @originalFile CustomValues.php
     */
    public function getByFid($id)
    {
        return $this->where('custom_values_field', $id)->get();
    }
    /**
     * @originalName getByColumn
     *
     * @originalFile CustomValues.php
     */
    public function getByColumn($id)
    {
        return $this->where('custom_field_id', $id)->get();
    }
    /**
     * @originalName getById
     *
     * @originalFile CustomValues.php
     */
    public function getById($id)
    {
        return $this->where('custom_values_id', $id)->get();
    }
    /**
     * @originalName getByIds
     *
     * @originalFile CustomValues.php
     */
    public function getByIds($ids)
    {
        if (empty($ids)) {
            return;
        }
        $ids = is_array($ids) ? $ids : explode(',', $ids);
        return $this->where_in('custom_values_id', $ids)->get();
    }
    /**
     * @originalName columnHasValue
     *
     * @originalFile CustomValues.php
     */
    public function columnHasValue($fid, $id)
    {
        $this->where('custom_field_id', $fid);
        $this->where('custom_values_id', $id);
        $this->get();
        return (bool) $this->numRows();
    }
    /**
     * @originalName grouped
     *
     * @originalFile CustomValues.php
     */
    public function grouped()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_custom_fields.*,ip_custom_values.*', false);
        $this->db->select('count(custom_field_label) as count');
        $this->db->group_by('ip_custom_fields.custom_field_id');
        return $this;
    }
    /**
     * @originalName defaultSelect
     *
     * @originalFile CustomValues.php
     */
    public function defaultSelect()
    {
        $this->db->select('ip_custom_fields.*,ip_custom_values.*', false);
    }
    /**
     * @originalName defaultJoin
     *
     * @originalFile CustomValues.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_custom_fields', 'ip_custom_values.custom_values_field = ip_custom_fields.custom_field_id', 'inner');
    }
    /**
     * @originalName defaultOrderBy
     *
     * @originalFile CustomValues.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('ip_custom_values.custom_values_value');
    }
    /**
     * @originalName defaultGroupBy
     *
     * @originalFile CustomValues.php
     */
    public function defaultGroupBy()
    {
        //$this->db->group_by('ip_custom_values.custom_values_field');
    }
}
