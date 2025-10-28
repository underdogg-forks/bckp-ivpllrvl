<?php

namespace Modules\Quotes\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\Quotes\Models\QuoteItem;
use Modules\Quotes\Models\QuoteItemAmount;

#[AllowDynamicProperties]
class QuoteItemAmountsService extends BaseService
{
    /**
     * @originalName calculate
     *
     * @originalFile QuoteItemAmount.php
     */
    public function calculate(int $item_id, array &$global_discount): void
    {
        $item          = QuoteItem::findOrFail($item_id);
        $item_subtotal = $item->item_quantity * $item->item_price;
        if (config('legacy_calculation')) {
            $item_tax_total      = $item_subtotal * ($item->item_tax_rate_percent / 100);
            $item_discount_total = $item->item_discount_amount * $item->item_quantity;
            $item_total          = $item_subtotal + $item_tax_total - $item_discount_total;
        } else {
            $item_discount = 0.0;
            if ($global_discount['amount'] != 0 && $global_discount['items_subtotal'] != 0) {
                $item_discount = round($global_discount['amount'] * ($item_subtotal / $global_discount['items_subtotal']), 2);
            }
            if ($global_discount['percent'] != 0) {
                $item_discount = round($item_subtotal * ($global_discount['percent'] / 100), 2);
            }
            $global_discount['item'] += $item_discount;
            $item_discount_total = $item->item_discount_amount * $item->item_quantity;
            $item_tax_total      = ($item_subtotal - $item_discount - $item_discount_total) * ($item->item_tax_rate_percent / 100);
            $item_total          = $item_subtotal - $item_discount - $item_discount_total + $item_tax_total;
        }
        QuoteItemAmount::updateOrCreate(
            ['item_id' => $item_id],
            [
                'item_subtotal'  => $item_subtotal,
                'item_tax_total' => $item_tax_total,
                'item_discount'  => $item_discount_total,
                'item_total'     => $item_total,
            ]
        );
    }
}
