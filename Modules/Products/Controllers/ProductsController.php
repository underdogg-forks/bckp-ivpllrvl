<?php

namespace Modules\Products\Controllers;

use AllowDynamicProperties;
use Illuminate\Http\Request;
use Modules\Core\Controllers\AdminController;
use Modules\Families\Services\FamiliesService;
use Modules\Products\Services\ProductsService;
use Modules\TaxRates\Services\TaxRatesService;
use Modules\Units\Services\UnitsService;

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

    /**
     * Display and process the product creation/edit form.
     *
     * Handles cancel redirects, validates submitted data and saves the product when valid,
     * prepares the form for editing an existing product (or aborts with 404 if the product
     * cannot be prepared), and provides families, units, and tax rates for the view.
     *
     * @param \Illuminate\Http\Request $request The current HTTP request.
     * @param int|null $id Optional product ID for editing; null when creating a new product.
     * @return \Illuminate\Contracts\View\View The products form view populated with `families`, `units`, and `tax_rates`.
     */
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
        $families  = (new FamiliesService())->getAll();
        $units     = (new UnitsService())->getAll();
        $tax_rates = (new TaxRatesService())->getAll();

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