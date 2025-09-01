<?php

namespace Modules\Products\Controllers;

use AllowDynamicProperties;
use Illuminate\Http\Request;
use Modules\Core\Controllers\AdminController;
use Modules\Families\Models\Family;
use Modules\Products\Models\Product;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    public $ajax_controller = true;

    /**
     * @originalName modalProductLookups
     *
     * @originalFile AjaxController.php
     */
    public function modalProductLookups(Request $request): \Illuminate\Contracts\View\View
    {
        $filter_product = $request->input('filter_product');
        $filter_family  = $request->input('filter_family');
        $reset_table    = $request->input('reset_table');

        $productsQuery = Product::newQuery();
        if ($filter_family) {
            $productsQuery->where('family_id', $filter_family);
        }
        if ($filter_product) {
            $productsQuery->where('name', 'like', "%{$filter_product}%");
        }
        $products              = $productsQuery->get();
        $families              = Family::query()->all();
        $default_item_tax_rate = config('settings.default_item_tax_rate', 0);
        $data                  = [
            'products'              => $products,
            'families'              => $families,
            'filter_product'        => $filter_product,
            'filter_family'         => $filter_family,
            'default_item_tax_rate' => $default_item_tax_rate,
        ];
        if ($filter_product || $filter_family || $reset_table) {
            return view('products.partial_product_table_modal', $data);
        }

        return view('products.modal_product_lookups', $data);
    }

    /**
     * @originalName processProductSelections
     *
     * @originalFile AjaxController.php
     */
    public function processProductSelections(Request $request): \Illuminate\Http\JsonResponse
    {
        $productIds = $request->input('product_ids', []);
        $products   = Product::query()->whereIn('id', $productIds)->get();
        foreach ($products as $product) {
            $product->product_price = number_format($product->product_price, 2);
        }

        return response()->json($products);
    }
}
