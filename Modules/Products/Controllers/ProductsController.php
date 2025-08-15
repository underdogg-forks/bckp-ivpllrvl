<?php

namespace Modules\Products\Controllers;

use Illuminate\Support\Facades\Log;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

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
        (new ProductsService())->paginate(site_url('products/index'), $page);
        $products = (new ProductsService())->result();
        return view('products.index', ['filter_display' => true, 'filter_placeholder' => trans('filter_products'), 'filter_method' => 'filter_products', 'products' => $products]);
    }

    /**
     * @originalName form
     *
     * @originalFile ProductsController.php
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect()->route('products');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ((new ProductsService())->runValidation()) {
            // GetController the db array
            $db_array = (new ProductsService())->dbArray();
            (new ProductsService())->save($id, $db_array);
            redirect()->route('products');
        }
        if ($id && ! $this->input->post('btn_submit') && ! (new ProductsService())->prepForm($id)) {
            show_404();
        }
        $this->load->model('families/mdl_families');
        $this->load->model('units/mdl_units');
        $this->load->model('tax_rates/mdl_tax_rates');
        return view('products.form', ['families' => (new FamiliesService())->get()->result(), 'units' => (new UnitsService())->get()->result(), 'tax_rates' => (new TaxRatesService())->get()->result()]);
    }

    /**
     * @originalName delete
     *
     * @originalFile ProductsController.php
     */
    public function delete($id)
    {
        (new ProductsService())->delete($id);
        redirect()->route('products');
    }
}
