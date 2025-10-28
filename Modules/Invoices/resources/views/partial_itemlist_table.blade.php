@php
    $invoice_disabled = $invoice->is_read_only != 1 ? '' : ' disabled="disabled"';
@endphp

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
            <td rowspan="2" class="td-icon">
                <i class="fa fa-arrows cursor-move"></i>
                @if($invoice->invoice_is_recurring)
                    <br />
                    <i title="@lang('recurring')"
                       class="js-item-recurrence-toggler cursor-pointer fa fa-calendar-o text-muted"></i>
                    <input type="hidden" name="item_is_recurring" value="" />
                @endif
            </td>

            <td class="td-text">
                <input type="hidden" name="invoice_id" value="{{ $invoice_id }}">
                <input type="hidden" name="item_id" value="">
                <input type="hidden" name="item_product_id" value="">
                <input type="hidden" name="item_task_id" class="item-task-id" value="">

                <div class="input-group">
                    <span class="input-group-addon">@lang('item')</span>
                    <input type="text" name="item_name"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                           value="">
                </div>
            </td>

            <td class="td-amount td-quantity">
                <div class="input-group">
                    <span class="input-group-addon">@lang('quantity')</span>
                    <input type="text" name="item_quantity"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors amount"
                           value="">
                </div>
            </td>

            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon">@lang('price')</span>
                    <input type="text" name="item_price"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors amount"
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
                    <select name="item_tax_rate_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors">
                        <option value="0">@lang('none')</option>
                        @foreach($tax_rates as $tax_rate)
                            <option value="{{ $tax_rate->tax_rate_id }}"
                                {{ $tax_rate->tax_rate_id == get_setting('default_item_tax_rate') ? 'selected' : '' }}>
                                {{ format_amount($tax_rate->tax_rate_percent) . '% - ' . $tax_rate->tax_rate_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </td>

            @if($legacy_calculation)
                @include('layout.partial.itemlist_table_item_discount_input')
            @endif

            <td class="td-icon text-right td-vert-middle">
                <button type="button"
                        class="btn_delete_item inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5"
                        title="@lang('delete')">
                    <i class="fa fa-trash-o text-danger"></i>
                </button>
            </td>
        </tr>

        <tr>
            @if($invoice->sumex_id == '')
                <td class="td-textarea">
                    <div class="input-group">
                        <span class="input-group-addon">@lang('description')</span>
                        <textarea name="item_description"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"></textarea>
                    </div>
                </td>
            @else
                <td class="td-date">
                    <div class="input-group">
                        <span class="input-group-addon">@lang('date')</span>
                        <input type="text" name="item_date"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors datepicker"
                               value="{{ now()->format('Y-m-d') }}" {!! $invoice_disabled !!}>
                    </div>
                </td>
            @endif
        </tr>
        </tbody>

        @foreach($items as $item)
            <tbody class="item">
            <tr>
                <td rowspan="2" class="td-icon">
                    <i class="fa fa-arrows cursor-move"></i>
                    @if($invoice->invoice_is_recurring)
                        @php
                            $item_recurrence_state = $item->item_is_recurring ? '1' : '0';
                            $item_recurrence_class = $item->item_is_recurring ? 'fa-calendar-check-o text-success' : 'fa-calendar-o text-muted';
                        @endphp
                        <br />
                        <i title="@lang('recurring')"
                           class="js-item-recurrence-toggler cursor-pointer fa {{ $item_recurrence_class }}"></i>
                        <input type="hidden" name="item_is_recurring" value="{{ $item_recurrence_state }}" />
                    @endif
                </td>

                <td class="td-text">
                    <input type="hidden" name="invoice_id" value="{{ $invoice_id }}">
                    <input type="hidden" name="item_id" value="{{ $item->item_id }}" {!! $invoice_disabled !!}>
                    <input type="hidden" name="item_task_id" class="item-task-id"
                           value="{{ $item->item_task_id ?? '' }}">
                    <input type="hidden" name="item_product_id" value="{{ $item->item_product_id }}">

                    <div class="input-group">
                        <span class="input-group-addon">@lang('item')</span>
                        <input type="text" name="item_name"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                               value="{!! $item->item_name !!}" {!! $invoice_disabled !!}>
                    </div>
                </td>

                <td class="td-amount td-quantity">
                    <div class="input-group">
                        <span class="input-group-addon">@lang('quantity')</span>
                        <input type="text" name="item_quantity"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors amount"
                               value="{{ format_quantity($item->item_quantity) }}" {!! $invoice_disabled !!}>
                    </div>
                </td>

                <td class="td-amount">
                    <div class="input-group">
                        <span class="input-group-addon">@lang('price')</span>
                        <input type="text" name="item_price"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors amount"
                               value="{{ format_amount($item->item_price) }}" {!! $invoice_disabled !!}>
                        <div class="input-group-addon">{{ get_setting('currency_symbol') }}</div>
                    </div>
                </td>

                @if(!$legacy_calculation)
                    @include('layout.partial.itemlist_table_item_discount_input', ['item' => $item])
                @endif

                <td class="td-amount">
                    <div class="input-group">
                        <span class="input-group-addon">@lang('tax_rate')</span>
                        <select name="item_tax_rate_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors" {!! $invoice_disabled !!}>
                            <option value="0">@lang('none')</option>
                            @foreach($tax_rates as $tax_rate)
                                <option value="{{ $tax_rate->tax_rate_id }}"
                                    {{ $tax_rate->tax_rate_id == $item->item_tax_rate_id ? 'selected' : '' }}>
                                    {{ format_amount($tax_rate->tax_rate_percent) . '% - ' . $tax_rate->tax_rate_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </td>

                @if($legacy_calculation)
                    @include('layout.partial.itemlist_table_item_discount_input', ['item' => $item])
                @endif

                <td class="td-icon text-right td-vert-middle">
                    @if($invoice->is_read_only != 1)
                        <button type="button"
                                class="btn_delete_item inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5"
                                title="@lang('delete')"
                                data-item-id="{{ $item->item_id }}">
                            <i class="fa fa-trash-o text-danger"></i>
                        </button>
                    @endif
                </td>
            </tr>

            <tr>
                @if($invoice->sumex_id == '')
                    <td class="td-textarea">
                        <div class="input-group">
                            <span class="input-group-addon">@lang('description')</span>
                            <textarea name="item_description"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors" {!! $invoice_disabled !!}>
                                    {!! $item->item_description !!}
                                </textarea>
                        </div>
                    </td>
                @else
                    <td class="td-date">
                        <div class="input-group">
                            <span class="input-group-addon">@lang('date')</span>
                            <input type="text" name="item_date"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors datepicker"
                                   value="{{ format_date($item->item_date) }}" {!! $invoice_disabled !!}>
                        </div>
                    </td>
                @endif

                <td class="td-amount">
                    <div class="input-group">
                        <span class="input-group-addon">@lang('product_unit')</span>
                        <select name="item_product_unit_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors">
                            <option value="0">@lang('none')</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->unit_id }}"
                                    {{ $unit->unit_id == $item->item_product_unit_id ? 'selected' : '' }}>
                                    {{ $unit->unit_name . '/' . $unit->unit_name_plrl }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </td>

                <td class="td-amount td-vert-middle">
                    <span>@lang('subtotal')</span><br />
                    <span name="subtotal" class="amount">{{ format_currency($item->item_subtotal) }}</span>
                </td>

                @if(!$legacy_calculation)
                    @include('layout.partial.itemlist_table_item_discount_show', ['item' => $item])
                @endif

                <td class="td-amount td-vert-middle">
                    <span>@lang('tax')</span><br />
                    <span name="item_tax_total" class="amount">{{ format_currency($item->item_tax_total) }}</span>
                </td>

                @if($legacy_calculation)
                    @include('layout.partial.itemlist_table_item_discount_show', ['item' => $item])
                @endif

                <td class="td-amount td-vert-middle">
                    <span>@lang('total')</span><br />
                    <span name="item_total" class="amount">{{ format_currency($item->item_total) }}</span>
                </td>
            </tr>
            </tbody>
        @endforeach

    </table>
</div>
