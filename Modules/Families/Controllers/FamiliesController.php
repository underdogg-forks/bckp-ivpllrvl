<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Families\Controllers;

use Modules\Core\Controllers\AdminController;
/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */
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
        $this->mdl_families->paginate(site_url('families/index'), $page);
        $families = $this->mdl_families->result();
        $this->layout->set(['filter_display' => true, 'filter_placeholder' => trans('filter_families'), 'filter_method' => 'filter_families', 'families' => $families]);
        $this->layout->buffer('content', 'families/index');
        $this->layout->render();
    }
    /**
     * @originalName form
     *
     * @originalFile FamiliesController.php
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('families');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ($this->input->post('is_update') == 0 && $this->input->post('family_name') != '') {
            $check = $this->db->get_where('ip_families', ['family_name' => $this->input->post('family_name')])->result();
            if (!empty($check)) {
                $this->session->set_flashdata('alert_error', trans('family_already_exists'));
                redirect('families/form');
            }
        }
        if ($this->mdl_families->runValidation()) {
            $this->mdl_families->save($id);
            redirect('families');
        }
        if ($id && !$this->input->post('btn_submit')) {
            if (!$this->mdl_families->prepForm($id)) {
                show_404();
            }
            $this->mdl_families->setFormValue('is_update', true);
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
        $this->mdl_families->delete($id);
        redirect('families');
    }
}
