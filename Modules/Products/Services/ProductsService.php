<?php

namespace Modules\Products\Services;

use AllowDynamicProperties;
use Illuminate\Http\Request;
use Modules\Core\Services\BaseService;
use Modules\Products\Models\Product;

#[AllowDynamicProperties]
class ProductsService extends BaseService
{
    public $table = 'ip_products';

    public $primary_key = 'ip_products.product_id';

    /**
     * Get a base Product query with relationships for select.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultSelect(): \Illuminate\Database\Eloquent\Builder
    {
        return Product::query()->with(['family', 'unit', 'taxRate']);
    }

    /**
     * Get a Product query ordered by family and product name.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultOrderBy(): \Illuminate\Database\Eloquent\Builder
    {
        return Product::query()->orderBy('family_name')->orderBy('product_name');
    }

    /**
     * Get a Product query with relationships (joins).
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultJoin(): \Illuminate\Database\Eloquent\Builder
    {
        return Product::query()->with(['family', 'unit', 'taxRate']);
    }

    /**
     * Filter products by SKU, name, or description using Eloquent.
     *
     * @param string $match
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function byProduct(string $match): \Illuminate\Database\Eloquent\Builder
    {
        return Product::query()
            ->where(function ($query) use ($match) {
                $query->where('product_sku', 'like', "%{$match}%")
                    ->orWhere('product_name', 'like', "%{$match}%")
                    ->orWhere('product_description', 'like', "%{$match}%");
            });
    }

    /**
     * Filter products by family using Eloquent.
     *
     * @param int $familyId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function byFamily(int $familyId): \Illuminate\Database\Eloquent\Builder
    {
        return Product::query()->where('family_id', $familyId);
    }

    /**
     * @originalName validationRules
     *
     * @originalFile Product.php
     */
    public function validationRules()
    {
        return [
            'product_sku'         => ['field' => 'product_sku', 'label' => trans('product_sku')],
            'product_name'        => ['field' => 'product_name', 'label' => trans('product_name'), 'rules' => 'required'],
            'product_description' => ['field' => 'product_description', 'label' => trans('product_description')],
            'product_price'       => ['field' => 'product_price', 'label' => trans('product_price'), 'rules' => 'required'],
            'purchase_price'      => ['field' => 'purchase_price', 'label' => trans('purchase_price')],
            'provider_name'       => ['field' => 'provider_name', 'label' => trans('provider_name')],
            'family_id'           => ['field' => 'family_id', 'label' => trans('family'), 'rules' => 'numeric'],
            'unit_id'             => ['field' => 'unit_id', 'label' => trans('unit'), 'rules' => 'numeric'],
            'tax_rate_id'         => ['field' => 'tax_rate_id', 'label' => trans('tax_rate'), 'rules' => 'numeric'],
            // Modules\Core\Libraries\Sumex
            'product_tariff' => ['field' => 'product_tariff', 'label' => trans('product_tariff')],
        ];
    }

    /**
     * @originalName dbArray
     *
     * @originalFile Product.php
     */
    public function dbArray(Request $request = null)
    {
        $db_array                   = parent::dbArray($request);
        $db_array['product_price']  = empty($db_array['product_price']) ? null : standardize_amount($db_array['product_price']);
        $db_array['purchase_price'] = empty($db_array['purchase_price']) ? null : standardize_amount($db_array['purchase_price']);
        $db_array['family_id']      = empty($db_array['family_id']) ? null : $db_array['family_id'];
        $db_array['unit_id']        = empty($db_array['unit_id']) ? null : $db_array['unit_id'];
        $db_array['tax_rate_id']    = empty($db_array['tax_rate_id']) ? null : $db_array['tax_rate_id'];

        return $db_array;
    }

    /**
     * Get products by array of IDs.
     *
     * @param array $ids
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByIds(array $ids)
    {
        return Product::query()->whereIn('product_id', $ids)->get();
    }
}
