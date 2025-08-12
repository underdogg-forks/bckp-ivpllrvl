<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Products\Controllers;

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
class ProductsController extends AdminController
{
    /**
     * ProductsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_products');
    }
    /**
     * @originalName index
     *
     * @originalFile ProductsController.php
     */
    public function index($page = 0)
    {
        $this->mdl_products->paginate(site_url('products/index'), $page);
        $products = $this->mdl_products->result();
        $this->layout->set(['filter_display' => true, 'filter_placeholder' => trans('filter_products'), 'filter_method' => 'filter_products', 'products' => $products]);
        $this->layout->buffer('content', 'products/index');
        $this->layout->render();
    }
    /**
     * @originalName form
     *
     * @originalFile ProductsController.php
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('products');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ($this->mdl_products->runValidation()) {
            // GetController the db array
            $db_array = $this->mdl_products->dbArray();
            $this->mdl_products->save($id, $db_array);
            redirect('products');
        }
        if ($id && !$this->input->post('btn_submit') && !$this->mdl_products->prepForm($id)) {
            show_404();
        }
        $this->load->model('families/mdl_families');
        $this->load->model('units/mdl_units');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->layout->set(['families' => $this->mdl_families->get()->result(), 'units' => $this->mdl_units->get()->result(), 'tax_rates' => $this->mdl_tax_rates->get()->result()]);
        $this->layout->buffer('content', 'products/form');
        $this->layout->render();
    }
    /**
     * @originalName delete
     *
     * @originalFile ProductsController.php
     */
    public function delete($id)
    {
        $this->mdl_products->delete($id);
        redirect('products');
    }
}
