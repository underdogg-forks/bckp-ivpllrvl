<?php

namespace Modules\Units\Controllers;

use AllowDynamicProperties;
use Illuminate\Http\Request;
use Modules\Core\Controllers\AdminController;
use Modules\Units\Models\Unit;
use Modules\Units\Services\UnitsService;

#[AllowDynamicProperties]
class UnitsController extends AdminController
{
    /**
     * Initialize the UnitsController.
     *
     * Ensures base initialization required by the AdminController.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @originalName index
     *
     * @originalFile UnitsController.php
     */
    public function index(Request $request, int $page = 0): \Illuminate\Contracts\View\View
    {
        $units = Unit::paginate(20);

        return view('units.index', ['units' => $units]);
    }

    /**
     * @originalName form
     *
     * @originalFile UnitsController.php
     */
    public function form(Request $request, $id = null): \Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View
    {
        if ($request->input('btn_cancel')) {
            return redirect()->route('units');
        }
        $data = $request->validate([
            'unit_name'      => 'required|string',
            'unit_name_plrl' => 'required|string',
            'is_update'      => 'nullable|boolean',
        ]);
        if (empty($id) && $data['is_update'] == 0) {
            $exists = (new UnitsService())->exists($data['unit_name']);
            if ($exists) {
                return redirect()->route('units.form')->with('alert_error', trans('unit_already_exists'));
            }
        }
        if ($request->isMethod('post')) {
            (new UnitsService())->save($data, $id);

            return redirect()->route('units');
        }
        $unit = $id ? Unit::find($id) : null;

        return view('units.form', ['unit' => $unit]);
    }

    /**
     * Delete a unit by its identifier.
     *
     * If a unit with the given id exists, it is removed; otherwise no action is taken.
     *
     * @param int|string $id the identifier of the unit to delete
     *
     * @return \Illuminate\Http\RedirectResponse redirects to the units index route
     */
    public function delete($id): \Illuminate\Http\RedirectResponse
    {
        (new UnitsService())->delete($id);

        return redirect()->route('units');
    }
}
