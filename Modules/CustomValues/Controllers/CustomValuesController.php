<?php

namespace Modules\CustomValues\Controllers;

use Illuminate\Support\Facades\Log;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class CustomValuesController extends AdminController
{
    /**
     * Custom_Values constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_custom_values');
    }

    /**
     * @originalName index
     *
     * @originalFile CustomValuesController.php
     */
    public function index($page = 0)
    {
        (new CustomValuesService())->grouped()->paginate(site_url('custom_values/index'), $page);
        $custom_values = (new CustomValuesService())->result();
        $this->load->model('custom_fields/mdl_custom_fields');
        // Determine which name of table custom field to load
        $custom_tables = (new CustomFieldsService())->customTables();
        // load positions by table name
        $positions = (new CustomFieldsService())->getPositions(true);
        return view('custom_values.index', ['filter_display' => true, 'filter_placeholder' => trans('filter_custom_values'), 'filter_method' => 'filter_custom_values', 'custom_tables' => $custom_tables, 'custom_values' => $custom_values, 'positions' => $positions]);
    }

    /**
     * @originalName field
     *
     * @originalFile CustomValuesController.php
     */
    public function field($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect()->route('custom_values');
        }
        $this->load->model('custom_fields/mdl_custom_fields');
        $field  = (new CustomFieldsService())->getById($id);
        $result = (new CustomValuesService())->getByFid($id)->result();
        // Determine which name of table custom field to load
        $custom_tables = (new CustomFieldsService())->customTables();
        $positions     = (new CustomFieldsService())->getPositions(true);
        $position      = $positions[$field->custom_field_table][$field->custom_field_location];
        unset($positions);
        return view('custom_values.field', ['filter_display' => true, 'filter_placeholder' => trans('filter_custom_values'), 'filter_method' => 'filter_custom_values_field', 'id' => $id, 'field' => $field, 'elements' => $result, 'custom_field_usage' => (new CustomFieldsService())->used($id), 'position' => $position, 'table' => $custom_tables[$field->custom_field_table]]);
    }

    /**
     * @originalName edit
     *
     * @originalFile CustomValuesController.php
     */
    public function edit($id = null)
    {
        $value = (new CustomValuesService())->getById($id)->row();
        $fid   = $value->custom_field_id;
        if ($this->input->post('btn_cancel')) {
            redirect('custom_values/field/' . $fid);
        }
        if ((new CustomValuesService())->runValidation()) {
            (new CustomValuesService())->save($id);
            redirect('custom_values/field/' . $fid);
        }
        $this->load->model('custom_fields/mdl_custom_fields');
        $positions = (new CustomFieldsService())->getPositions(true);
        $position  = $positions[$value->custom_field_table][$value->custom_field_location];
        unset($positions);
        return view('custom_values.edit', ['id' => $id, 'fid' => $fid, 'value' => $value, 'position' => $position, 'custom_field_usage' => (new CustomValuesService())->used($id)]);
    }

    /**
     * @originalName create
     *
     * @originalFile CustomValuesController.php
     */
    public function create($id = null)
    {
        if ( ! $id) {
            redirect()->route('custom_values');
        }
        $fid = $id;
        if ($this->input->post('btn_cancel')) {
            redirect('custom_values/field/' . $fid);
        }
        if ((new CustomValuesService())->runValidation()) {
            (new CustomValuesService())->saveCustom($fid);
            redirect('custom_values/field/' . $fid);
        }
        $this->load->model('custom_fields/mdl_custom_fields');
        $field = (new CustomFieldsService())->getById($id);
        // Determine which name of table custom field to load
        $custom_tables = (new CustomFieldsService())->customTables();
        $table         = $custom_tables[$field->custom_field_table];
        unset($custom_tables);
        $positions = (new CustomFieldsService())->getPositions(true);
        $position  = $positions[$field->custom_field_table][$field->custom_field_location];
        unset($positions);
        return view('custom_values.new', ['id' => $id, 'field' => $field, 'table' => $table, 'position' => $position]);
    }

    /**
     * @originalName delete
     *
     * @originalFile CustomValuesController.php
     */
    public function delete($id)
    {
        if ( ! (new CustomValuesService())->delete($id)) {
            $this->session->set_flashdata('alert_info', trans('id') . sprintf(' "%s" ', $id) . trans('custom_values_used_not_deletable'));
        }
        $fid = $this->input->post('custom_field_id');
        redirect('custom_values' . ($fid ? '/field/' . $fid : ''));
    }
}
