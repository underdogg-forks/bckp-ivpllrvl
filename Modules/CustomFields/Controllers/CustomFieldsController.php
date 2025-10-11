<?php

namespace Modules\CustomFields\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class CustomFieldsController extends AdminController
{
    /**
     * Initialize the CustomFieldsController.
     *
     * Calls the parent AdminController constructor to set up base controller state.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @originalName index
     *
     * @originalFile CustomFieldsController.php
     */
    public function index(): void
    {
        // Display all custom_fields tables by default
        redirect()->route('custom_fields/table/all');
    }

    /**
     * Render the custom fields list view for a specific table (or all) with pagination.
     *
     * The rendered view receives filter controls, the selected table's custom fields,
     * the list of available custom tables, available custom value fields, and position options.
     *
     * @param string $name The custom table name to filter by, or 'all' to show all tables.
     * @param int $page The pagination page number to display.
     */
    public function table(string $name = 'all', $page = 0): void
    {
        // Determine which name of table custom field to load
        $custom_tables = (new CustomFieldsService())->customTables();
        if ($name != 'all' && in_array($name, $custom_tables)) {
            (new CustomFieldsService())->byTableName($name);
        }
        // Paginate before result
        (new CustomFieldsService())->paginate(site_url('custom_fields/name/' . $name), $page);
        $custom_fields = (new CustomFieldsService())->result();

        return view('custom_fields.index', ['filter_display' => true, 'filter_placeholder' => trans('filter_custom_fields'), 'filter_method' => 'filter_custom_fields', 'custom_fields' => $custom_fields, 'custom_tables' => $custom_tables, 'custom_value_fields' => (new CustomValuesService())->customValueFields(), 'positions' => (new CustomFieldsService())->getPositions(true)]);
    }

    /**
     * @originalName form
     *
     * @originalFile CustomFieldsController.php
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect()->route('custom_fields');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ((new CustomFieldsService())->runValidation()) {
            (new CustomFieldsService())->save($id);
            redirect()->route('custom_fields');
        }
        if ($id && ! $this->input->post('btn_submit') && ! (new CustomFieldsService())->prepForm($id)) {
            show_404();
        }

        return view('custom_fields.form', ['custom_field_id' => $id, 'custom_field_tables' => (new CustomFieldsService())->customTables(), 'custom_field_types' => (new CustomFieldsService())->customTypes(), 'custom_field_usage' => (new CustomFieldsService())->used($id), 'custom_field_location' => (new CustomFieldsService())->formValue('custom_field_location'), 'positions' => (new CustomFieldsService())->getPositions()]);
    }

    /**
     * @originalName delete
     *
     * @originalFile CustomFieldsController.php
     */
    public function delete($id)
    {
        if ( ! (new CustomFieldsService())->delete($id)) {
            $this->session->set_flashdata('alert_info', trans('id') . sprintf(' "%s" ', $id) . trans('custom_fields_used_not_deletable'));
        }
        // Return to page number of custom values or fields
        $r = empty($_SERVER['HTTP_REFERER']) ? 'custom_fields' : $_SERVER['HTTP_REFERER'];
        redirect($r);
    }
}