<?php

namespace Modules\Products\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    public $ajax_controller = true;

    /**
     * @originalName modalProductLookups
     *
     * @originalFile AjaxController.php
     */
    public function modalProductLookups()
    {
        $filter_product = $this->input->get('filter_product', true);
        $filter_family  = $this->input->get('filter_family', true);
        $reset_table    = $this->input->get('reset_table', true);
        $this->load->model('mdl_products');
        $this->load->model('families/mdl_families');
        if ( ! empty($filter_family)) {
            (new ProductsService())->byFamily($filter_family);
            $filter_family = $this->security->xss_clean($filter_family);
        }
        if ( ! empty($filter_product)) {
            (new ProductsService())->byProduct($filter_product);
            $filter_product = $this->security->xss_clean($filter_product);
        }
        $products              = (new ProductsService())->get()->result();
        $families              = (new FamiliesService())->get()->result();
        $default_item_tax_rate = get_setting('default_item_tax_rate');
        $default_item_tax_rate = $default_item_tax_rate !== '' ?: 0;
        $data                  = ['products' => $products, 'families' => $families, 'filter_product' => $filter_product, 'filter_family' => $filter_family, 'default_item_tax_rate' => $default_item_tax_rate];
        if ($filter_product || $filter_family || $reset_table) {
            $this->layout->loadView('products/partial_product_table_modal', $data);
        } else {
            $this->layout->loadView('products/modal_product_lookups', $data);
        }
    }

    /**
     * @originalName processProductSelections
     *
     * @originalFile AjaxController.php
     */
    public function processProductSelections()
    {
        $this->load->model('mdl_products');
        $products = (new ProductsService())->where_in('product_id', $this->input->post('product_ids'))->get()->result();
        foreach ($products as $product) {
            $product->product_price = format_amount($product->product_price);
        }
        echo json_encode($products);
    }
}
