<?php

namespace Modules\CustomValues\Controllers;

use AllowDynamicProperties;
use Illuminate\Http\Request;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class CustomValuesController extends AdminController
{
    /**
     * Initialize the CustomValuesController and invoke the parent controller constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @originalName index
     *
     * @originalFile CustomValuesController.php
     */
    public function index(Request $request, int $page = 0): \Illuminate\Contracts\View\View
    {
        $custom_values = (new CustomValuesService())->grouped()->paginate(route('custom_values.index'), $page)->result();
        $custom_tables = (new CustomFieldsService())->customTables();
        $positions     = (new CustomFieldsService())->getPositions(true);

        return view('custom_values.index', [
            'filter_display'     => true,
            'filter_placeholder' => trans('filter_custom_values'),
            'filter_method'      => 'filter_custom_values',
            'custom_tables'      => $custom_tables,
            'custom_values'      => $custom_values,
            'positions'          => $positions,
        ]);
    }

    /**
     * @originalName field
     *
     * @originalFile CustomValuesController.php
     */
    public function field(Request $request, $id = null): \Illuminate\Contracts\View\View
    {
        if ($request->input('btn_cancel')) {
            return redirect()->route('custom_values');
        }
        $field         = (new CustomFieldsService())->getById($id);
        $result        = (new CustomValuesService())->getByFid($id)->result();
        $custom_tables = (new CustomFieldsService())->customTables();
        $positions     = (new CustomFieldsService())->getPositions(true);
        $position      = $positions[$field->custom_field_table][$field->custom_field_location];

        return view('custom_values.field', [
            'filter_display'     => true,
            'filter_placeholder' => trans('filter_custom_values'),
            'filter_method'      => 'filter_custom_values_field',
            'id'                 => $id,
            'field'              => $field,
            'elements'           => $result,
            'custom_field_usage' => (new CustomFieldsService())->used($id),
            'position'           => $position,
            'table'              => $custom_tables[$field->custom_field_table],
        ]);
    }

    /**
     * @originalName edit
     *
     * @originalFile CustomValuesController.php
     */
    public function edit(Request $request, $id = null): \Illuminate\Contracts\View\View
    {
        $value = (new CustomValuesService())->getById($id)->row();
        $fid   = $value->custom_field_id;
        if ($request->input('btn_cancel')) {
            return redirect('custom_values/field/' . $fid);
        }
        if ((new CustomValuesService())->runValidation()) {
            (new CustomValuesService())->save($id);

            return redirect('custom_values/field/' . $fid);
        }
        $positions = (new CustomFieldsService())->getPositions(true);
        $position  = $positions[$value->custom_field_table][$value->custom_field_location];

        return view('custom_values.edit', [
            'id'                 => $id,
            'fid'                => $fid,
            'value'              => $value,
            'position'           => $position,
            'custom_field_usage' => (new CustomValuesService())->used($id),
        ]);
    }

    /**
     * @originalName create
     *
     * @originalFile CustomValuesController.php
     */
    public function create(Request $request, $id = null): \Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View
    {
        if ( ! $id) {
            return redirect()->route('custom_values');
        }
        $fid = $id;
        if ($request->input('btn_cancel')) {
            return redirect('custom_values/field/' . $fid);
        }
        if ((new CustomValuesService())->runValidation()) {
            (new CustomValuesService())->saveCustom($fid);

            return redirect('custom_values/field/' . $fid);
        }

        // Show create view if not submitted
        return view('custom_values.create', ['fid' => $fid]);
    }

    /**
     * Delete a custom value identified by its id and redirect to the custom values list or the field-specific page.
     *
     * If deletion fails because the value is in use, a flash alert is set describing the failure.
     *
     * @param int|string $id The identifier of the custom value to delete.
     * @return \CodeIgniter\HTTP\RedirectResponse|\Illuminate\Http\RedirectResponse Redirect to the custom values index or to the field page when `custom_field_id` is present in the request.
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