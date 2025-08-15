<?php

namespace Modules\Families\Controllers;

use Illuminate\Support\Facades\Log;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\Families\Services\FamiliesService;

#[AllowDynamicProperties]
class FamiliesController extends AdminController
{
    /**
     * FamiliesController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_families');
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
        if ($this->input->post('btn_cancel')) {
            redirect()->route('families');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ($this->input->post('is_update') == 0 && $this->input->post('family_name') != '') {
            $check = $this->db->get_where('ip_families', ['family_name' => $this->input->post('family_name')])->result();
            if ( ! empty($check)) {
                $this->session->set_flashdata('alert_error', trans('family_already_exists'));
                redirect()->route('families/form');
            }
        }
        if ((new FamiliesService())->runValidation()) {
            (new FamiliesService())->save($id);
            redirect()->route('families');
        }
        if ($id && ! $this->input->post('btn_submit')) {
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
