<?php

namespace Modules\Families\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\Families\Services\FamiliesService;

#[AllowDynamicProperties]
class FamiliesController extends AdminController
{
    /**
     * Initialize the FamiliesController and inherit the setup from AdminController.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @originalName index
     *
     * @originalFile FamiliesController.php
     */
    public function index($page = 0): View
    {
        (new FamiliesService())->paginate(site_url('families/index'), $page);
        $families = (new FamiliesService())->result();

        return view('families.index', ['filter_display' => true, 'filter_placeholder' => trans('filter_families'), 'filter_method' => 'filter_families', 'families' => $families]);
    }

    /**
     * @originalName form
     *
     * @originalFile FamiliesController.php
     */
    public function form(Request $request, $id = null): View|RedirectResponse
    {
        if ($request->post('btn_cancel')) {
            return redirect()->route('families');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ($request->post('is_update') == 0 && $request->post('family_name') != '') {
            $check = $this->db->get_where('ip_families', ['family_name' => $request->post('family_name')])->result();
            if ( ! empty($check)) {
                $this->session->set_flashdata('alert_error', trans('family_already_exists'));
                return redirect()->route('families.form');
            }
        }
        if ((new FamiliesService())->runValidation(null, $request)) {
            (new FamiliesService())->save($request, $id);
            return redirect()->route('families');
        }
        if ($id && ! $request->post('btn_submit')) {
            if ( ! (new FamiliesService())->prepForm($id)) {
                abort(404);
            }
            (new FamiliesService())->setFormValue('is_update', true);
        }
        return view('families.form');
    }

    /**
     * @originalName delete
     *
     * @originalFile FamiliesController.php
     */
    public function delete($id): RedirectResponse
    {
        (new FamiliesService())->delete($id);
        return redirect()->route('families');
    }
}
