<?php

namespace Modules\CustomFields\Services;

use Illuminate\Support\Facades\DB;
use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\CustomFields\Models\ClientCustom;

#[AllowDynamicProperties]
class ClientCustomsService extends BaseService
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
     * Save custom field values for a client.
     *
     * Validates the provided data; if validation passes, writes each value from the service's
     * form data (`$this->_formdata`) to the client's custom fields. If no form data is present,
     * the method completes successfully without writing. If validation fails, the validation
     * result is returned unchanged.
     *
     * @param int   $client_id the client identifier
     * @param array $db_array  data used for validation
     *
     * @return mixed `true` on successful save (or when no form data is present), otherwise the validation result
     */
    public function saveCustom($client_id, $db_array)
    {
        $result = $this->validate($db_array);
        if ($result === true) {
            $form_data = property_exists($this, '_formdata') && $this->_formdata !== null ? $this->_formdata : null;
            if (null === $form_data) {
                return true;
            }
            foreach ($form_data as $key => $value) {
                \Modules\Clients\Models\ClientCustom::query()->updateOrCreate(
                    [
                        'client_id'             => $client_id,
                        'client_custom_fieldid' => $key,
                    ],
                    [
                        'client_id'                => $client_id,
                        'client_custom_fieldid'    => $key,
                        'client_custom_fieldvalue' => $value,
                    ]
                );
            }

            return true;
        }

        return $result;
    }

    /**
     * Populate the form with a client's custom field values.
     *
     * When a client ID is provided, retrieves the client's custom fields, formats each value
     * according to its custom field type, and sets the corresponding form inputs using keys
     * in the format `cf_{custom_field_id}`. After populating custom values, delegates to
     * parent::prepForm($id).
     *
     * @param int|null $id the client ID whose custom field values should be loaded; pass null to skip loading
     */
    public function prepForm($id = null)
    {
        if ($id) {
            $values = $this->getByClient($id);
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
     * Retrieve all custom field values for a client joined with their field definitions.
     *
     * @param int $client_id the ID of the client to retrieve custom values for
     *
     * @return \Illuminate\Database\Eloquent\Collection collection of records combining ip_client_custom and ip_custom_fields for the specified client, ordered by custom_field_table, custom_field_order, and custom_field_label
     */
    public function getByClid($client_id)
    {
        return ClientCustom::query()
            ->select('ip_client_custom.*', 'ip_custom_fields.*')
            ->join('ip_custom_fields', 'ip_client_custom.client_custom_fieldid', '=', 'ip_custom_fields.custom_field_id')
            ->where('ip_client_custom.client_id', $client_id)
            ->orderBy('custom_field_table')
            ->orderBy('custom_field_order')
            ->orderBy('custom_field_label')
            ->get();
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
