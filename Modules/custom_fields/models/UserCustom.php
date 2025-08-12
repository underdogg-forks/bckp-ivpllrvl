<?php

namespace Modules\Customfields\Models;

use Modules\Core\Validators\Validator;

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * userPlane
 *
 * @author      userPlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2017 userPlane.com
 * @license     https://userplane.com/license.txt
 * @link        https://userplane.com
 */
#[AllowDynamicProperties]
class UserCustom extends Validator
{
    public static $positions = ['custom_fields', 'account_information', 'address', 'tax_information', 'contact_information'];

    public $table = 'ip_user_custom';

    public $primary_key = 'ip_user_custom.user_custom_id';

    /**
     * @originalName defaultSelect
     *
     * @originalFile UserCustom.php
     */
    public function defaultSelect()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_user_custom.*, ip_custom_fields.*', false);
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile UserCustom.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_custom_fields', 'ip_user_custom.user_custom_fieldid = ip_custom_fields.custom_field_id');
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile UserCustom.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('custom_field_table ASC, custom_field_order ASC, custom_field_label ASC');
    }

    /**
     * @originalName saveCustom
     *
     * @originalFile UserCustom.php
     */
    public function saveCustom($user_id, $db_array)
    {
        $result = $this->validate($db_array);
        if ($result === true) {
            $form_data = property_exists($this, '_formdata') && $this->_formdata !== null ? $this->_formdata : null;
            if (null === $form_data) {
                return true;
            }
            $user_custom_id = null;
            foreach ($form_data as $key => $value) {
                $db_array    = ['user_id' => $user_id, 'user_custom_fieldid' => $key, 'user_custom_fieldvalue' => $value];
                $user_custom = $this->where('user_id', $user_id)->where('user_custom_fieldid', $key)->get();
                if ($user_custom->num_rows()) {
                    $user_custom_id = $user_custom->row()->user_custom_id;
                }
                parent::save($user_custom_id, $db_array);
            }

            return true;
        }

        return $result;
    }

    /**
     * @originalName byId
     *
     * @originalFile UserCustom.php
     */
    public function byId($user_id)
    {
        $this->db->where('ip_user_custom.user_id', $user_id);

        return $this;
    }

    /**
     * @originalName getByUseid
     *
     * @originalFile UserCustom.php
     */
    public function getByUseid($user_id)
    {
        return $this->where('ip_user_custom.user_id', $user_id)->get()->result();
    }
}
