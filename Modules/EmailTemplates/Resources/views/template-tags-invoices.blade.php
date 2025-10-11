
<div class="mb-4">
    <label for="tags_invoice">@lang('invoices')</label>
    <select id="tags_invoice" class="tag-select w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors">
        <option value="{{{invoice_number}" }}>
            @lang('id')
        </option>
        <option value="{{{invoice_status}" }}>
            @lang('status')
        </option>
        <optgroup label="@lang('invoice_dates')">
            <option value="{{{invoice_date_due}" }}>
                @lang('due_date')
            </option>
            <option value="{{{invoice_date_created}" }}>
                @lang('invoice_date')
            </option>
        </optgroup>
        <optgroup label="@lang('invoice_amounts')">
            <option value="{{{invoice_item_subtotal}" }}>
                @lang('subtotal')
            </option>
            <option value="{{{invoice_item_tax_total}" }}>
                @lang('invoice_tax')
            </option>
            <option value="{{{invoice_total}" }}>
                @lang('total')
            </option>
            <option value="{{{invoice_paid}" }}>
                @lang('total_paid')
            </option>
            <option value="{{{invoice_balance}" }}>
                @lang('balance')
            </option>
        </optgroup>
        <optgroup label="@lang('extra_information')">
            <option value="{{{invoice_terms}" }}>
                @lang('invoice_terms')
            </option>
            <option value="{{{invoice_guest_url}" }}>
                @lang('guest_url')
            </option>
            <!--                 <option value="{{{payment_method}" }}> -->
            <!--                     @lang('payment_method') -->
            <!--                 </option> -->
        </optgroup>
        @if($custom_fields['ip_invoice_custom'])
        <optgroup label="@lang('custom_fields')">
            @foreach($custom_fields['ip_invoice_custom'] as $custom)
            <option value="{{{{{ 'ip_cf_' . $custom->custom_field_id }}}" }}>
                {{ $custom->custom_field_label . ' (ID ' . $custom->custom_field_id . ')' }}
            </option>@endforeach
        </optgroup>@endforeach
    </select>
</div>
    