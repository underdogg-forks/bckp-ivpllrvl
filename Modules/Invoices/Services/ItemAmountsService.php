<?php

namespace Modules\Invoices\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\Invoices\Models\ItemAmount;

#[AllowDynamicProperties]
class ItemAmountsService extends BaseService
{
    /**
     * Create a new ItemAmountsService with the provided ItemsService dependency.
     *
     * @param ItemsService $itemsService service used to retrieve and manage invoice items
     */
    public function __construct(public ItemsService $itemsService)
    {
        parent::__construct();
    }

    /**
     * Compute and store subtotal, discount, tax, and total for an invoice item and add the item's share of any global discount to the provided accumulator.
     *
     * The computed values are persisted to the item's amount record and the per-item portion of a global discount is added to `$global_discount['item']`.
     *
     * @param int $item_id the invoice item identifier
     * @param array & $global_discount Reference to the global discount data. The array is modified by this method by incrementing the `item` key. Expected keys:
     *                                - 'amount' (float): total global discount amount,
     *                                - 'items_subtotal' (float): sum of item subtotals used for proportional distribution,
     *                                - 'percent' (float): global discount percent (used when non-zero),
     *                                - 'item' (float): accumulator for per-item discounts (will be incremented).
     */
    public function calculate($item_id, &$global_discount)
    {
        $item          = $this->itemsService->getById($item_id);
        $item_subtotal = $item->item_quantity * $item->item_price;
        // Legacy calculation - discounts - since v1.6.3
        if (config('legacy_calculation')) {
            $item_tax_total      = $item_subtotal * ($item->item_tax_rate_percent / 100);
            $item_discount_total = $item->item_discount_amount * $item->item_quantity;
            $item_total          = $item_subtotal + $item_tax_total - $item_discount_total;
        } else {
            $item_discount = 0.0;
            // For total & tax calculation after all discounts applied Proportionally by item
            if ($global_discount['amount'] != 0 && $global_discount['items_subtotal'] != 0) {
                // Prevent divide per 0
                $item_discount = round($global_discount['amount'] * ($item_subtotal / $global_discount['items_subtotal']), 2);
            }
            if ($global_discount['percent'] != 0) {
                // Percent per default
                $item_discount = round($item_subtotal * ($global_discount['percent'] / 100), 2);
            }
            $global_discount['item'] += $item_discount;
            // for Mdl_invoice_amounts calculation
            $item_discount_total = $item->item_discount_amount * $item->item_quantity;
            $item_tax_total      = ($item_subtotal - $item_discount - $item_discount_total) * ($item->item_tax_rate_percent / 100);
            $item_total          = $item_subtotal - $item_discount - $item_discount_total + $item_tax_total;
        }
        $db_array = ['item_id' => $item_id, 'item_subtotal' => $item_subtotal, 'item_tax_total' => $item_tax_total, 'item_discount' => $item_discount_total, 'item_total' => $item_total];

        ItemAmount::query()->updateOrCreate(
            ['item_id' => $item_id],
            $db_array
        );
    }
}
