
<tr>
    <td class="td-vert-middle">@lang('global_discount')</td>
    <td class="clear-both">
        <div class="discount-field">
            <div class="input-group input-group-sm">
                <input id="quote_discount_amount" name="quote_discount_amount"
                       class="discount-option w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors amount" aria-label="@lang('global_discount')"
                       value="{{ format_amount($quote->quote_discount_amount != 0 ? $quote->quote_discount_amount : '') " }}>
                <span class="input-group-addon">{{ get_setting('currency_symbol') }}</span>
            </div>
        </div>
        <div class="discount-field">
            <div class="input-group input-group-sm">
                <input id="quote_discount_percent" name="quote_discount_percent" aria-label="@lang('global_discount') %"
                       value="{{ format_amount($quote->quote_discount_percent != 0 ? $quote->quote_discount_percent : '') }}"
                       class="discount-option w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors amount">
                <span class="input-group-addon">%</span>
            </div>
        </div>
    </td>
</tr>
