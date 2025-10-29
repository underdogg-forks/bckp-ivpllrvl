<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\DB;

/*
 * userPlane
 *
 * @author      userPlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2017 userPlane.com
 * @license     https://userplane.com/license.txt
 * @link        https://userplane.com
 */
#[AllowDynamicProperties]
class UserCustomsService extends BaseService
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
        DB::select('SQL_CALC_FOUND_ROWS ip_user_custom.*, ip_custom_fields.*', false);
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile UserCustom.php
     */
    public function defaultJoin()
    {
        DB::join('ip_custom_fields', 'ip_user_custom.user_custom_fieldid = ip_custom_fields.custom_field_id');
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile UserCustom.php
     */
    public function defaultOrderBy()
    {
        DB::orderBy('custom_field_table ASC, custom_field_order ASC, custom_field_label ASC');
    }

    /**
     * Persist custom field values from the service's form data for the specified user.
     *
     * @param int|string $user_id  the identifier of the user to save custom fields for
     * @param array      $db_array data passed to the validator
     *
     * @return true|mixed `true` if validation passed and form data (if any) were saved or there was nothing to save; otherwise the validation error information returned by validate()
     */
    public function saveCustom($user_id, $db_array)
    {
        $result = $this->validate($db_array);
        if ($result === true) {
            $form_data = property_exists($this, '_formdata') && $this->_formdata !== null ? $this->_formdata : null;
            if (null === $form_data) {
                return true;
            }
            foreach ($form_data as $key => $value) {
                \src\Models\UserCustom::query()->updateOrCreate(
                    [
                        'user_id'             => $user_id,
                        'user_custom_fieldid' => $key,
                    ],
                    [
                        'user_id'                => $user_id,
                        'user_custom_fieldid'    => $key,
                        'user_custom_fieldvalue' => $value,
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
     * @originalFile UserCustom.php
     */
    public function byId($user_id)
    {
        DB::where('ip_user_custom.user_id', $user_id);

        return $this;
    }

    /**
     * Retrieve custom field records for a specific user.
     *
     * @param int $user_id the user's ID to filter records by
     *
     * @return array an array of matching user custom field records
     */
    public function getByUseid($user_id)
    {
        return $this->where('ip_user_custom.user_id', $user_id)->get()->all();
    }
}
