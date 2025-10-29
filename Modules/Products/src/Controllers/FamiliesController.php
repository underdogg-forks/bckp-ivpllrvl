<?php

namespace src\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

use function Modules\Families\Controllers\show_404;
use function Modules\Families\Controllers\site_url;

use src\Services\FamiliesService;

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
    public function index($page = 0)
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
    public function form($id = null)
    {
        if (request()->input('btn_cancel')) {
            redirect()->route('families');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if (request()->input('is_update') == 0 && request()->input('family_name') != '') {
            $check = $this->db->get_where('ip_families', ['family_name' => request()->input('family_name')])->result();
            if ( ! empty($check)) {
                session()->flash('alert_error', trans('family_already_exists'));
                redirect()->route('families/form');
            }
        }
        if ((new FamiliesService())->runValidation()) {
            (new FamiliesService())->save($id);
            redirect()->route('families');
        }
        if ($id && ! request()->input('btn_submit')) {
            if ( ! (new FamiliesService())->prepForm($id)) {
                show_404();
            }
            (new FamiliesService())->setFormValue('is_update', true);
        }
        $this->layout->buffer('content', 'families/form');
        $this->layout->render();
    }

    /**
     * @originalName delete
     *
     * @originalFile FamiliesController.php
     */
    public function delete($id)
    {
        (new FamiliesService())->delete($id);
        redirect()->route('families');
    }
}
