

<div class="overflow-x-auto">
    <table id="item_table" class="items min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm table-bordered no-margin">

        <thead style="display:none">
        <tr>
            <th></th>
            <th>@lang('item')</th>
            <!--
            <th>@lang('description')</th>
-->
            <th class="amount">@lang('quantity')</th>
            <th class="amount">@lang('price')</th>
            {{ $legacy_calculation ? '' : '<th class="amount">' . trans('item_discount') . '</th>' }}
            <th class="amount">@lang('tax_rate')</th>
            {{ $legacy_calculation ? '<th class="amount">' . trans('item_discount') . '</th>' : '' }}
            <!--
            <th class="amount">@lang('subtotal')</th>
            <th class="amount">@lang('tax')</th>
-->
            <th class="amount">@lang('total')</th>
            <th></th>
        </tr>
        </thead>

        <tbody id="new_row" style="display:none">
        <tr>
            <td rowspan="2" class="td-icon"><i class="fa fa-arrows cursor-move"></i></td>
            <td class="td-text">
                <input type="hidden" name="quote_id" value="{{ $quote_id " }}>
                <input type="hidden" name="item_id" value="">
                <input type="hidden" name="item_product_id" value="">

                <div class="input-group">
                    <span class="input-group-addon">@lang('item')</span>
                    <input type="text" name="item_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors" value="">
                </div>
            </td>
            <td class="td-amount td-quantity">
                <div class="input-group">
                    <span class="input-group-addon">@lang('quantity')</span>
                    <input type="text" name="item_quantity" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors amount" value="">
                </div>
            </td>
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon">@lang('price')</span>
                    <input type="text" name="item_price" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors amount" value="">
                    <div class="input-group-addon">{{ get_setting('currency_symbol') }}</div>
                </div>
            </td>
            @if(!$legacy_calculation) {
    $this->layout->loadView('layout/partial/itemlist_table_item_discount_input');
}
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon">@lang('tax_rate')</span>
                    <select name="item_tax_rate_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors">
                        <option value="0">@lang('none')</option>
                        @foreach($tax_rates as $tax_rate)
                        <option value="{{ $tax_rate->tax_rate_id " }}>
                            {{ format_amount($tax_rate->tax_rate_percent) . '% - ' . $tax_rate->tax_rate_name }}
                        </option>@endforeach
                    </select>
                </div>
            </td>
            @if($legacy_calculation) {
    $this->layout->loadView('layout/partial/itemlist_table_item_discount_input');
}
            <td class="td-icon text-right td-vert-middle">
                <button type="button" class="btn_delete_item inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5" title="@lang('delete')">
                    <i class="fa fa-trash-o text-danger"></i>
                </button>
            </td>
        </tr>
        <tr>
            <td class="td-textarea">
                <div class="input-group">
                    <span class="input-group-addon">@lang('description')</span>
                    <textarea name="item_description" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"></textarea>
                </div>
            </td>
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon">@lang('product_unit')</span>
                    <select name="item_product_unit_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors">
                        <option value="0">@lang('none')</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->unit_id " }}>
                            {!! $unit->unit_name) . '/' . htmlsc($unit->unit_name_plrl !!}
                        </option>@endforeach
                    </select>
                </div>
            </td>
            <td class="td-amount td-vert-middle">
                <span>@lang('subtotal')</span><br/>
                <span name="subtotal" class="amount"></span>
            </td>
            @if(!$legacy_calculation) {
    $this->layout->loadView('layout/partial/itemlist_table_item_discount_show');
}
            <td class="td-amount td-vert-middle">
                <span>@lang('tax')</span><br/>
                <span name="item_tax_total" class="amount"></span>
            </td>
            @if($legacy_calculation) {
    $this->layout->loadView('layout/partial/itemlist_table_item_discount_show');
}
            <td class="td-amount td-vert-middle">
                <span>@lang('total')</span><br/>
                <span name="item_total" class="amount"></span>
            </td>
        </tr>
        </tbody>

        @foreach($items as $item)
        <tbody class="item">
        <tr>
            <td rowspan="2" class="td-icon"><i class="fa fa-arrows cursor-move"></i></td>
            <td class="td-text">
                <input type="hidden" name="quote_id" value="{{ $quote_id " }}>
                <input type="hidden" name="item_id" value="{{ $item->item_id " }}>
                <input type="hidden" name="item_product_id" value="{{ $item->item_product_id " }}>

                <div class="input-group">
                    <span class="input-group-addon">@lang('item')</span>
                    <input type="text" name="item_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                           value="{!! $item->item_name !!}">
                </div>
            </td>
            <td class="td-amount td-quantity">
                <div class="input-group">
                    <span class="input-group-addon">@lang('quantity')</span>
                    <input type="text" name="item_quantity" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors amount"
                           value="{{ format_quantity($item->item_quantity) " }}>
                </div>
            </td>
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon">@lang('price')</span>
                    <input type="text" name="item_price" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors amount"
                           value="{{ format_amount($item->item_price) " }}>
                    <div class="input-group-addon">{{ get_setting('currency_symbol') }}</div>
                </div>
            </td>
            @if(!$legacy_calculation) {
                    $this->layout->loadView('layout/partial/itemlist_table_item_discount_input', ['item' => $item]);
                }

            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon">@lang('tax_rate')</span>
                    <select name="item_tax_rate_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors">
                        <option value="0">@lang('none')</option>
                        @foreach($tax_rates as $tax_rate) {
                                $is_selected = $item->item_tax_rate_id == $tax_rate->tax_rate_id ? ' selected="selected"' : '';

                        <option value="{{ $tax_rate->tax_rate_id }}"{{ $is_selected }}>
                            {{ format_amount($tax_rate->tax_rate_percent) . '% - ' . htmlsc($tax_rate->tax_rate_name) }}
                        </option>@endforeach
                    </select>
                </div>
            </td>
            @if($legacy_calculation) {
                    $this->layout->loadView('layout/partial/itemlist_table_item_discount_input', ['item' => $item]);
                }

            <td class="td-icon text-right td-vert-middle">
                <button type="button" class="btn_delete_item inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5" title="@lang('delete')"
                        data-item-id="{{ $item->item_id " }}>
                    <i class="fa fa-trash-o text-danger"></i>
                </button>
            </td>
        </tr>
        <tr>
            <td class="td-textarea">
                <div class="input-group">
                    <span class="input-group-addon">@lang('description')</span>
                    <textarea name="item_description" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                    >{!! $item->item_description !!}</textarea>
                </div>
            </td>
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon">@lang('product_unit')</span>
                    <select name="item_product_unit_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors">
                        <option value="0">@lang('none')</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->unit_id }}"
                            @php
                                check_select($item->item_product_unit_id, $unit->unit_id)>
                            {!! $unit->unit_name) . '/' . htmlsc($unit->unit_name_plrl !!}
                        </option>@endforeach
                    </select>
                </div>
            </td>
            <td class="td-amount td-vert-middle">
                <span>@lang('subtotal')</span><br/>
                <span name="subtotal" class="amount">
                        {{ format_currency($item->item_subtotal) }}
                    </span>
            </td>
            @if(!$legacy_calculation) {
                    $this->layout->loadView('layout/partial/itemlist_table_item_discount_show', ['item' => $item]);
                }

            <td class="td-amount td-vert-middle">
                <span>@lang('tax')</span><br/>
                <span name="item_tax_total" class="amount">
                        {{ format_currency($item->item_tax_total) }}
                    </span>
            </td>
            @if($legacy_calculation) {
                    $this->layout->loadView('layout/partial/itemlist_table_item_discount_show', ['item' => $item]);
                }

            <td class="td-amount td-vert-middle">
                <span>@lang('total')</span><br/>
                <span name="item_total" class="amount">
                        {{ format_currency($item->item_total) }}
                    </span>
            </td>
        </tr>
        </tbody>
        @php
            }
            // End foreach items

    </table>
</div>

<br>

<div class="flex flex-wrap -mx-4">
    <div class="w-full px-4 md:w-1/3">
        <div class="inline-flex rounded-md shadow-sm">
            <a href="javascript:void(0);" class="btn_add_row inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fa fa-plus"></i>
                @lang('add_new_row')
            </a>
            <a href="javascript:void(0);" class="btn_add_product inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fa fa-database"></i>
                @lang('add_product')
            </a>
        </div>
    </div>

    <div class="w-full px-4 block sm:hidden hidden sm:block md:hidden"><br></div>

    <div class="w-full px-4 md:w-1/2 col-md-offset-2 lg:w-1/3 col-lg-offset-4">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700 text-right">
            @if(!$legacy_calculation) {
    $this->layout->loadView('quotes/partial_itemlist_table_quote_discount');
}
            <tr>
                <td style="width: 40%;">@lang('subtotal')</td>
                <td style="width: 60%;" class="amount">{{ format_currency($quote->quote_item_subtotal) }}</td>
            </tr>
            <tr>
                <td>@lang('item_tax')</td>
                <td class="amount">{{ format_currency($quote->quote_item_tax_total) }}</td>
            </tr>
            @if($legacy_calculation)
            <tr>
                <td>@lang('quote_tax')</td>
                <td>
                    @if($quote_tax_rates) {
                            @foreach($quote_tax_rates as $quote_tax_rate)
                    <form method="POST" class="flex flex-wrap gap-4 items-center"
                          action="{{ url('quotes/delete_quote_tax/' . $quote->quote_id . '/' . $quote_tax_rate->quote_tax_rate_id) " }}>
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                onclick="var Y=confirm('@lang('delete_tax_warning')');if(Y)show_loader();return Y;">
                            <i class="fa fa-trash-o"></i>
                        </button>
                        <span class="text-muted">
                            {!! $quote_tax_rate->quote_tax_rate_name) . ' ' . format_amount($quote_tax_rate->quote_tax_rate_percent) . '%' }}
                        </span>
                        <span class="amount">
                            {{ format_currency($quote_tax_rate->quote_tax_rate_amount !!}
                        </span>
                    </form>
                    @php
                        }
                    } else {
                        echo format_currency('0');
                    }

                </td>
            </tr>
                <?php
                $this->layout->loadView('quotes/partial_itemlist_table_quote_discount');
            }
            <tr>
                <td><b>@lang('total')</b></td>
                <td class="amount"><b>{{ format_currency($quote->quote_total) }}</b></td>
            </tr>
        </table>
    </div>

</div>
