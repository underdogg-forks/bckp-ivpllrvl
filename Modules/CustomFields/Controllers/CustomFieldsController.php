<?php

namespace Modules\CustomFields\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\CustomFields\Services\CustomFieldsService;
use Modules\CustomValues\Services\CustomValuesService;

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
     * Redirects to the view that lists all custom fields tables.
     *
     * Performs an immediate redirect to the 'custom_fields/table/all' route.
     */
    public function index(): RedirectResponse
    {
        // Display all custom_fields tables by default
        return redirect('custom_fields/table/all');
    }

    /**
     * Render the custom fields list view filtered by table name with pagination.
     *
     * When $name is not 'all' and exists, only fields for that table are shown. The view is provided with filter controls, the paginated custom fields, available custom tables, custom value fields, and position options.
     *
     * @param string $name the custom table name to filter by, or 'all' to show all tables
     * @param int    $page the pagination page number to display
     */
    public function table(string $name = 'all', int $page = 0): View
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
    public function form(Request $request, $id = null): View|RedirectResponse
    {
        if ($request->post('btn_cancel')) {
            return redirect()->route('custom_fields');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ((new CustomFieldsService())->runValidation(null, $request)) {
            (new CustomFieldsService())->save($request, $id);
            return redirect()->route('custom_fields');
        }
        if ($id && ! $request->post('btn_submit') && ! (new CustomFieldsService())->prepForm($id)) {
            abort(404);
        }

        return view('custom_fields.form', ['custom_field_id' => $id, 'custom_field_tables' => (new CustomFieldsService())->customTables(), 'custom_field_types' => (new CustomFieldsService())->customTypes(), 'custom_field_usage' => (new CustomFieldsService())->used($id), 'custom_field_location' => (new CustomFieldsService())->formValue('custom_field_location'), 'positions' => (new CustomFieldsService())->getPositions()]);
    }

    /**
     * Attempts to delete a custom field identified by `$id` and then redirects back to the referring page.
     *
     * If the field cannot be deleted, a flash message is set to inform the user that the field is in use.
     *
     * @param int|string $id the custom field identifier to delete
     */
    public function delete($id): RedirectResponse
    {
        if ( ! (new CustomFieldsService())->delete($id)) {
            $this->session->set_flashdata('alert_info', trans('id') . sprintf(' "%s" ', $id) . trans('custom_fields_used_not_deletable'));
        }
        // Return to page number of custom values or fields
        $r = empty($_SERVER['HTTP_REFERER']) ? 'custom_fields' : $_SERVER['HTTP_REFERER'];
        return redirect()->to($r);
    }
}
