<?php

namespace Modules\Products\Controllers;

use AllowDynamicProperties;
use Illuminate\Http\Request;
use Modules\Core\Controllers\AdminController;
use Modules\Families\Models\Family;
use Modules\Products\Services\ProductsService;
use Modules\TaxRates\Models\TaxRate;
use Modules\Units\Models\Unit;

#[AllowDynamicProperties]
class ProductsController extends AdminController
{
    public function index(Request $request, int $page = 0): \Illuminate\Contracts\View\View
    {
        $service = new ProductsService();
        $service->paginate(route('products.index'), $page);
        $products = $service->result();

        return view('products.index', [
            'filter_display'     => true,
            'filter_placeholder' => trans('filter_products'),
            'filter_method'      => 'filter_products',
            'products'           => $products,
        ]);
    }

    public function form(Request $request, $id = null): \Illuminate\Contracts\View\View
    {
        if ($request->has('btn_cancel')) {
            return redirect()->route('products.index');
        }
        // Filter input if needed
        // Validation
        $service = new ProductsService();
        if ($service->runValidation()) {
            $db_array = $service->dbArray();
            $service->save($id, $db_array);

            return redirect()->route('products.index');
        }
        if ($id && ! $request->has('btn_submit') && ! $service->prepForm($id)) {
            abort(404);
        }
        $families  = Family::all();
        $units     = Unit::all();
        $tax_rates = TaxRate::all();

        return view('products.form', [
            'families'  => $families,
            'units'     => $units,
            'tax_rates' => $tax_rates,
        ]);
    }

    public function delete($id)
    {
        (new ProductsService())->delete($id);

        return redirect()->route('products.index');
    }
}
