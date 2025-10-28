// Called in [quotes|invoices]/partial_itemlist_table.php (item & new) line
$invoice_disabled = isset($invoice) && $invoice->is_read_only == 1 ? ' disabled="disabled"' : '';
$item_value = isset($item->item_discount_amount) ? format_amount($item->item_discount_amount) : '';
<td class="td-amount">
    <div class="input-group">
        <span class="input-group-addon">@lang('discount')</span>
        <input type="text" name="item_discount_amount"
               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors amount"
               value="{{ $item_value }}" {{ $invoice_disabled }}
               data-toggle="tooltip" data-placement="bottom" title="@lang('item_discount')">
        <span class="input-group-addon">{{ get_setting('currency_symbol') . ' itemlist_table_item_discount_input.blade.php' . trans('per_item') }}</span>
    </div>
</td>
