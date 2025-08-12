<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Units\Controllers;

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */
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
        $this->mdl_units->paginate(site_url('units/index'), $page);
        $units = $this->mdl_units->result();
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
            redirect('units');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ($this->input->post('is_update') == 0 && $this->input->post('unit_name') != '' && $this->input->post('unit_name_plrl') != '') {
            $check = $this->db->get_where('ip_units', ['unit_name' => $this->input->post('unit_name')])->result();
            if (!empty($check)) {
                $this->session->set_flashdata('alert_error', trans('unit_already_exists'));
                redirect('units/form');
            }
        }
        if ($this->mdl_units->runValidation()) {
            $this->mdl_units->save($id);
            redirect('units');
        }
        if ($id && !$this->input->post('btn_submit')) {
            if (!$this->mdl_units->prepForm($id)) {
                show_404();
            }
            $this->mdl_units->setFormValue('is_update', true);
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
        $this->mdl_units->delete($id);
        redirect('units');
    }
}
