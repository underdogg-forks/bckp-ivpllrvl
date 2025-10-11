<?php

namespace Modules\Invoices\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\Invoices\Models\Item;
use Modules\Invoices\Models\ItemAmount;

#[AllowDynamicProperties]
class ItemsService extends BaseService
{
    public $table = 'ip_invoice_items';

    public $primary_key = 'ip_invoice_items.item_id';

    public $date_created_field = 'item_date_added';

    /**
     * Construct the ItemsService and register its item and invoice amount services.
     *
     * @param ItemAmountsService    $itemAmountsService    service responsible for calculating and managing amounts for individual invoice items
     * @param InvoiceAmountsService $invoiceAmountsService service responsible for calculating and managing invoice-level amounts and global discounts
     */
    public function __construct(
        public ItemAmountsService $itemAmountsService,
        public InvoiceAmountsService $invoiceAmountsService
    ) {
        parent::__construct();
    }

    /**
     * @originalName defaultSelect
     *
     * @originalFile Item.php
     */
    public function defaultSelect()
    {
        $this->db->select('ip_invoice_item_amounts.*, ip_products.*, ip_invoice_items.*,
            item_tax_rates.tax_rate_percent AS item_tax_rate_percent,
            item_tax_rates.tax_rate_name AS item_tax_rate_name');
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile Item.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('ip_invoice_items.item_order');
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile Item.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_invoice_item_amounts', 'ip_invoice_item_amounts.item_id = ip_invoice_items.item_id', 'left');
        $this->db->join('ip_tax_rates AS item_tax_rates', 'item_tax_rates.tax_rate_id = ip_invoice_items.item_tax_rate_id', 'left');
        $this->db->join('ip_products', 'ip_products.product_id = ip_invoice_items.item_product_id', 'left');
    }

    /**
     * @originalName validationRules
     *
     * @originalFile Item.php
     */
    public function validationRules()
    {
        return ['invoice_id' => ['field' => 'invoice_id', 'label' => trans('invoice'), 'rules' => 'required'], 'item_sku' => ['field' => 'item_sku', 'label' => trans('item_sku'), 'rules' => 'required|unique'], 'item_name' => ['field' => 'item_name', 'label' => trans('item_name'), 'rules' => 'required'], 'item_description' => ['field' => 'item_description', 'label' => trans('description')], 'item_quantity' => ['field' => 'item_quantity', 'label' => trans('quantity'), 'rules' => 'required'], 'item_price' => ['field' => 'item_price', 'label' => trans('price'), 'rules' => 'required'], 'item_tax_rate_id' => ['field' => 'item_tax_rate_id', 'label' => trans('item_tax_rate')], 'item_product_id' => ['field' => 'item_product_id', 'label' => trans('original_product')], 'item_date' => ['field' => 'item_date', 'label' => trans('item_date')], 'item_is_recurring' => ['field' => 'item_is_recurring', 'label' => trans('recurring')]];
    }

    /**
     * Persist an invoice item and update related monetary totals.
     *
     * Saves the item record and recalculates the item's amounts; if `invoice_id` is present in
     * `$db_array` (object or array), recalculates the corresponding invoice amounts as well.
     *
     * @param int|null          $id               the item ID to update, or null to create a new item
     * @param array|object|null $db_array         data used to save the item; may contain `invoice_id`
     * @param array             &$global_discount Reference to a global discount array that may be read/updated during recalculation
     *
     * @return int the saved item ID
     */
    public function save($id = null, $db_array = null, &$global_discount = [])
    {
        $id = parent::save($id, $db_array);
        $this->itemAmountsService->calculate($id, $global_discount);
        if (is_object($db_array) && isset($db_array->invoice_id)) {
            $this->invoiceAmountsService->calculate($db_array->invoice_id, $global_discount);
        } elseif (is_array($db_array) && isset($db_array['invoice_id'])) {
            $this->invoiceAmountsService->calculate($db_array['invoice_id'], $global_discount);
        }

        return $id;
    }

    /**
     * Deletes an invoice item and updates related invoice amounts.
     *
     * Deletes the specified invoice item and its associated item-amount records, updates the invoice-level global discount from the invoice amounts service, and recalculates the invoice totals.
     *
     * @param int $item_id the ID of the invoice item to delete
     *
     * @return bool `true` if the item existed and was deleted, `false` if no matching item was found
     */
    public function delete($item_id): bool
    {
        // Get item using Eloquent
        $item = Item::query()->where('item_id', $item_id)->first();

        if ( ! $item) {
            return false;
        }

        $invoice_id = $item->invoice_id;

        // Delete the item itself using parent delete
        parent::delete($item_id);

        // Delete the item amounts using Eloquent
        ItemAmount::query()->where('item_id', $item_id)->delete();

        $global_discount['item'] = $this->invoiceAmountsService->getGlobalDiscount($invoice_id);
        // Recalculate invoice amounts
        $this->invoiceAmountsService->calculate($invoice_id, $global_discount);

        return true;
    }

    /**
     * Calculate the sum of `item_subtotal` for all items belonging to the specified invoice.
     *
     * @param int|string $invoice_id the invoice identifier
     *
     * @return float the summed `item_subtotal` value for the invoice; returns `0` if there are no matching items
     */
    public function getItemsSubtotal($invoice_id)
    {
        $result = \Illuminate\Support\Facades\DB::table('ip_invoice_item_amounts')
            ->whereIn('item_id', function ($query) use ($invoice_id) {
                $query->select('item_id')
                    ->from('ip_invoice_items')
                    ->where('invoice_id', $invoice_id);
            })
            ->sum('item_subtotal');

        return $result;
    }

    /**
     * Retrieve all item records belonging to the specified invoice.
     *
     * @param int $invoice_id ID of the invoice to fetch items for
     *
     * @return \Illuminate\Database\Eloquent\Collection collection of Item models for the invoice
     */
    public function getByInvoiceId($invoice_id)
    {
        return \Modules\Invoices\Models\Item::query()->where('invoice_id', $invoice_id)->get();
    }
}
