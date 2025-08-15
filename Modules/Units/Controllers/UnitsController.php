<?php

namespace Modules\Units\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class UnitsController extends AdminController
{
    /**
     * UnitsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_units');
    }

    /**
     * @originalName index
     *
     * @originalFile UnitsController.php
     */
    public function index($page = 0)
    {
        (new UnitsService())->paginate(site_url('units/index'), $page);
        $units = (new UnitsService())->result();
        $this->layout->set('units', $units);
        $this->layout->buffer('content', 'units/index');
        $this->layout->render();
    }

    /**
     * @originalName form
     *
     * @originalFile UnitsController.php
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect()->route('units');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ($this->input->post('is_update') == 0 && $this->input->post('unit_name') != '' && $this->input->post('unit_name_plrl') != '') {
            $check = $this->db->get_where('ip_units', ['unit_name' => $this->input->post('unit_name')])->result();
            if ( ! empty($check)) {
                $this->session->set_flashdata('alert_error', trans('unit_already_exists'));
                redirect()->route('units/form');
            }
        }
        if ((new UnitsService())->runValidation()) {
            (new UnitsService())->save($id);
            redirect()->route('units');
        }
        if ($id && ! $this->input->post('btn_submit')) {
            if ( ! (new UnitsService())->prepForm($id)) {
                show_404();
            }
            (new UnitsService())->setFormValue('is_update', true);
        }
        $this->layout->buffer('content', 'units/form');
        $this->layout->render();
    }

    /**
     * @originalName delete
     *
     * @originalFile UnitsController.php
     */
    public function delete($id)
    {
        (new UnitsService())->delete($id);
        redirect()->route('units');
    }
}
