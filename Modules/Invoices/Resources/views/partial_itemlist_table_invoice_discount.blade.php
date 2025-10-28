@php
$discountInputClasses = 'discount-option w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-brand-primary sm:text-sm transition-colors amount';
$invoice_disabled = $invoice->is_read_only != 1 ? '' : ' disabled="disabled"';
@endphp
<tr>
    <td class="td-vert-middle">@lang('global_discount')</td>
    <td class="clear-both">
        <div class="discount-field">
            <div class="input-group input-group-sm">
                <input id="invoice_discount_amount" name="invoice_discount_amount" aria-label="@lang('global_discount')"
                       value="{{ format_amount($invoice->invoice_discount_amount != 0 ? $invoice->invoice_discount_amount : '') }}"
                       class="{{ $discountInputClasses }}"{{ $invoice_disabled }}>
                <span class="input-group-addon">{{ get_setting('currency_symbol') }}</span>
            </div>
        </div>
        <div class="discount-field">
            <div class="input-group input-group-sm">
                <input id="invoice_discount_percent" name="invoice_discount_percent"
                       aria-label="@lang('global_discount') %"
                       value="{{ format_amount($invoice->invoice_discount_percent != 0 ? $invoice->invoice_discount_percent : '') }}"
                       class="{{ $discountInputClasses }}"{{ $invoice_disabled }}>
                <span class="input-group-addon">%</span>
            </div>
        </div>
    </td>
</tr>
