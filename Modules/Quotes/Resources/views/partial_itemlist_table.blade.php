<div class="overflow-x-auto">
    <table id="item_table"
           class="items min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm table-bordered no-margin">

        <thead style="display:none">
        <tr>
            <th></th>
            <th>@lang('item')</th>
            <th class="amount">@lang('quantity')</th>
            <th class="amount">@lang('price')</th>
            @if(!$legacy_calculation)
                <th class="amount">@lang('item_discount')</th>
            @endif
            <th class="amount">@lang('tax_rate')</th>
            @if($legacy_calculation)
                <th class="amount">@lang('item_discount')</th>
            @endif
            <th class="amount">@lang('total')</th>
            <th></th>
        </tr>
        </thead>

        <tbody id="new_row" style="display:none">
        <tr>
            <td rowspan="2" class="td-icon"><i class="fa fa-arrows cursor-move"></i></td>
            <td class="td-text">
                <input type="hidden" name="quote_id" value="{{ $quote_id }}">
                <input type="hidden" name="item_id" value="">
                <input type="hidden" name="item_product_id" value="">
                <div class="input-group">
                    <span class="input-group-addon">@lang('item')</span>
                    <input type="text" name="item_name"
                           class="w-full px-3 py-2 border rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:text-sm transition-colors"
                           value="">
                </div>
            </td>
            <td class="td-amount td-quantity">
                <div class="input-group">
                    <span class="input-group-addon">@lang('quantity')</span>
                    <input type="text" name="item_quantity" class="w-full px-3 py-2 border rounded-md shadow-sm amount"
                           value="">
                </div>
            </td>
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon">@lang('price')</span>
                    <input type="text" name="item_price" class="w-full px-3 py-2 border rounded-md shadow-sm amount"
                           value="">
                    <div class="input-group-addon">{{ get_setting('currency_symbol') }}</div>
                </div>
            </td>
            @if(!$legacy_calculation)
                @include('layout.partial.itemlist_table_item_discount_input')
            @endif
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon">@lang('tax_rate')</span>
                    <select name="item_tax_rate_id" class="w-full px-3 py-2 border rounded-md shadow-sm">
                        <option value="0">@lang('none')</option>
                        @foreach($tax_rates as $tax_rate)
                            <option value="{{ $tax_rate->tax_rate_id }}">
                                {{ format_amount($tax_rate->tax_rate_percent) . '% - ' . htmlsc($tax_rate->tax_rate_name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </td>
            @if($legacy_calculation)
                @include('layout.partial.itemlist_table_item_discount_input')
            @endif
            <td class="td-icon text-right td-vert-middle">
                <button type="button" class="btn_delete_item" title="@lang('delete')">
                    <i class="fa fa-trash-o text-danger"></i>
                </button>
            </td>
        </tr>
        <tr>
            <td class="td-textarea">
                <div class="input-group">
                    <span class="input-group-addon">@lang('description')</span>
                    <textarea name="item_description" class="w-full px-3 py-2 border rounded-md shadow-sm"></textarea>
                </div>
            </td>
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon">@lang('product_unit')</span>
                    <select name="item_product_unit_id" class="w-full px-3 py-2 border rounded-md shadow-sm">
                        <option value="0">@lang('none')</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->unit_id }}">
                                {{ $unit->unit_name . '/' . htmlsc($unit->unit_name_plrl) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td class="td-amount td-vert-middle">
                <span>@lang('subtotal')</span><br />
                <span name="subtotal" class="amount"></span>
            </td>
            @if(!$legacy_calculation)
                @include('layout.partial.itemlist_table_item_discount_show')
            @endif
            <td class="td-amount td-vert-middle">
                <span>@lang('tax')</span><br />
                <span name="item_tax_total" class="amount"></span>
            </td>
            @if($legacy_calculation)
                @include('layout.partial.itemlist_table_item_discount_show')
            @endif
            <td class="td-amount td-vert-middle">
                <span>@lang('total')</span><br />
                <span name="item_total" class="amount"></span>
            </td>
        </tr>
        </tbody>

        @foreach($items as $item)
            <tbody class="item">
            <tr>
                <td rowspan="2" class="td-icon"><i class="fa fa-arrows cursor-move"></i></td>
                <td class="td-text">
                    <input type="hidden" name="quote_id" value="{{ $quote_id }}">
                    <input type="hidden" name="item_id" value="{{ $item->item_id }}">
                    <input type="hidden" name="item_product_id" value="{{ $item->item_product_id }}">
                    <div class="input-group">
                        <span class="input-group-addon">@lang('item')</span>
                        <input type="text" name="item_name" class="w-full px-3 py-2 border rounded-md shadow-sm"
                               value="{{ $item->item_name }}">
                    </div>
                </td>
                <td class="td-amount td-quantity">
                    <input type="text" name="item_quantity" class="w-full px-3 py-2 border rounded-md shadow-sm amount"
                           value="{{ format_quantity($item->item_quantity) }}">
                </td>
                <td class="td-amount">
                    <input type="text" name="item_price" class="w-full px-3 py-2 border rounded-md shadow-sm amount"
                           value="{{ format_amount($item->item_price) }}">
                </td>
                @if(!$legacy_calculation)
                    @include('layout.partial.itemlist_table_item_discount_input', ['item' => $item])
                @endif
                <td class="td-amount">
                    <select name="item_tax_rate_id" class="w-full px-3 py-2 border rounded-md shadow-sm">
                        <option value="0">@lang('none')</option>
                        @foreach($tax_rates as $tax_rate)
                            <option
                                value="{{ $tax_rate->tax_rate_id }}" {{ $item->item_tax_rate_id == $tax_rate->tax_rate_id ? 'selected' : '' }}>
                                {{ format_amount($tax_rate->tax_rate_percent) . '% - ' . htmlsc($tax_rate->tax_rate_name) }}
                            </option>
                        @endforeach
                    </select>
                </td>
                @if($legacy_calculation)
                    @include('layout.partial.itemlist_table_item_discount_input', ['item' => $item])
                @endif
                <td class="td-icon text-right td-vert-middle">
                    <button type="button" class="btn_delete_item" title="@lang('delete')"
                            data-item-id="{{ $item->item_id }}">
                        <i class="fa fa-trash-o text-danger"></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td class="td-textarea">
                    <textarea name="item_description"
                              class="w-full px-3 py-2 border rounded-md shadow-sm">{{ $item->item_description }}</textarea>
                </td>
                <td class="td-amount">
                    <select name="item_product_unit_id" class="w-full px-3 py-2 border rounded-md shadow-sm">
                        <option value="0">@lang('none')</option>
                        @foreach($units as $unit)
                            <option
                                value="{{ $unit->unit_id }}" {{ $item->item_product_unit_id == $unit->unit_id ? 'selected' : '' }}>
                                {{ $unit->unit_name . '/' . htmlsc($unit->unit_name_plrl) }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td class="td-amount td-vert-middle">{{ format_currency($item->item_subtotal) }}</td>
                @if(!$legacy_calculation)
                    @include('layout.partial.itemlist_table_item_discount_show', ['item' => $item])
                @endif
                <td class="td-amount td-vert-middle">{{ format_currency($item->item_tax_total) }}</td>
                @if($legacy_calculation)
                    @include('layout.partial.itemlist_table_item_discount_show', ['item' => $item])
                @endif
                <td class="td-amount td-vert-middle">{{ format_currency($item->item_total) }}</td>
            </tr>
            </tbody>
        @endforeach
    </table>
</div>
