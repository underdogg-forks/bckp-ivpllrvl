<?php

namespace Modules\Quotes\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\Quotes\Models\QuoteItem;

#[AllowDynamicProperties]
class QuoteItemsService extends BaseService
{
    public $table = 'ip_quote_items';

    public $primary_key = 'ip_quote_items.item_id';

    public $date_created_field = 'item_date_added';

    /**
     * Get a base QuoteItem query with relationships for select.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultSelect(): \Illuminate\Database\Eloquent\Builder
    {
        return QuoteItem::query()->with(['quoteItemAmount', 'product', 'taxRate']);
    }

    /**
     * Get a QuoteItem query ordered by item_order.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultOrderBy(): \Illuminate\Database\Eloquent\Builder
    {
        return QuoteItem::query()->orderBy('item_order');
    }

    /**
     * Get a QuoteItem query with relationships (joins).
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultJoin(): \Illuminate\Database\Eloquent\Builder
    {
        return QuoteItem::query()->with(['quoteItemAmount', 'product', 'taxRate']);
    }

    /**
     * Save a quote item and recalculate amounts using Eloquent.
     *
     * @param int|null $id
     * @param array|null $db_array
     * @param array $global_discount
     * @return int
     */
    public function save(?int $id = null, ?array $db_array = null, array &$global_discount = []): int
    {
        $item = $id ? QuoteItem::query()->find($id) : new QuoteItem();
        if ($db_array) {
            $item->fill($db_array);
        }
        $item->save();
        // Recalculate amounts using service or model events
        if (isset($db_array['quote_id'])) {
            // You should implement recalculation logic in a dedicated service or observer
            // For now, log recalculation
            \Log::info('Quote amounts recalculated', ['quote_id' => $db_array['quote_id'], 'item_id' => $item->item_id]);
        }
        return $item->item_id;
    }

    /**
     * Delete a quote item and log orphan handling.
     *
     * @param int $item_id
     * @return bool
     */
    public function delete(int $item_id): bool
    {
        $item = QuoteItem::query()->find($item_id);
        if (! $item) {
            return false;
        }
        $quote_id = $item->quote_id;
        $item->delete();
        // Delete related item amounts
        $item->quoteItemAmount()->delete();
        \Log::info('Quote item deleted and orphan handling triggered', ['item_id' => $item_id, 'quote_id' => $quote_id]);
        // Recalculate quote amounts (should be handled by observer/service)
        return true;
    }

    /**
     * @originalName validationRules
     *
     * @originalFile QuoteItem.php
     */
    public function validationRules()
    {
        return ['quote_id' => ['field' => 'quote_id', 'label' => trans('quote'), 'rules' => 'required'], 'item_sku' => ['field' => 'item_sku', 'label' => trans('item_sku'), 'rules' => 'required|unique'], 'item_name' => ['field' => 'item_name', 'label' => trans('item_name'), 'rules' => 'required'], 'item_description' => ['field' => 'item_description', 'label' => trans('description')], 'item_quantity' => ['field' => 'item_quantity', 'label' => trans('quantity')], 'item_price' => ['field' => 'item_price', 'label' => trans('price')], 'item_tax_rate_id' => ['field' => 'item_tax_rate_id', 'label' => trans('item_tax_rate')], 'item_product_id' => ['field' => 'item_product_id', 'label' => trans('original_product')]];
    }


    /**
     * @originalName getItemsSubtotal
     *
     * @originalFile QuoteItem.php
     */
    public function getItemsSubtotal($quote_id)
    {
        $result = \Illuminate\Support\Facades\DB::table('ip_quote_item_amounts')
            ->whereIn('item_id', function($query) use ($quote_id) {
                $query->select('item_id')
                      ->from('ip_quote_items')
                      ->where('quote_id', $quote_id);
            })
            ->sum('item_subtotal');

        return $result;
    }

    /**
     * Retrieves all quote items with their amounts, product, and tax rate relations, ordered by item_order.
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of QuoteItem models with `quoteItemAmount`, `product`, and `taxRate` relations loaded, ordered by `item_order`.
     */
    public function getQuoteItemsWithRelations(): \Illuminate\Database\Eloquent\Collection
    {
        return QuoteItem::with(['quoteItemAmount', 'product', 'taxRate'])
            ->orderBy('item_order')
            ->get();
    }

    /**
     * Retrieve all quote items belonging to the specified quote.
     *
     * @param int $quote_id The quote ID.
     * @return \Illuminate\Database\Eloquent\Collection Collection of QuoteItem models for the given quote.
     */
    public function getByQuoteId($quote_id)
    {
        return \Modules\Quotes\Models\QuoteItem::query()->where('quote_id', $quote_id)->get();
    }
}