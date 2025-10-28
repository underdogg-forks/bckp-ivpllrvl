@php
    $invoice_disabled = $invoice->is_read_only != 1 ? '' : ' disabled="disabled"';
@endphp

<div class="flex flex-wrap -mx-4">
    <div id="item_table" class="items table w-full px-4">
        <div id="new_row" class="mb-4 details-box" style="display: none;">
            <div class="flex flex-wrap -mx-4">
                <div class="w-full px-4 col-sm-7 md:w-1/2 col-lg-5">
                    <div class="flex flex-wrap -mx-4">
                        <div class="w-full px-4 col-sm-1">
                            <button type="button"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors up"
                                    title="@lang('move_up')">
                                <i class="fa fa-chevron-up"></i>
                            </button>
                            <button type="button"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors down"
                                    title="@lang('move_down')">
                                <i class="fa fa-chevron-down"></i>
                            </button>

                            @if($invoice->invoice_is_recurring)
                                <i title="{{ trans('recurring') }}"
                                   class="js-item-recurrence-toggler cursor-pointer fa fa-calendar-o text-muted"></i>
                                <input type="hidden" name="item_is_recurring" value="" />
                            @endif

                            <button type="button"
                                    class="btn_delete_item inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5"
                                    title="@lang('delete')">
                                <i class="fa fa-trash-o text-danger"></i>
                            </button>
                        </div>

                        <div class="w-full px-4 col-sm-11">
                            <div class="input-group">
                                <label for="item_name" class="input-group-addon ig-addon-aligned">@lang('item')</label>
                                <input type="text" name="item_name" id="item_name"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="">
                            </div>

                            <div class="input-group">
                                @if($invoice->sumex_id == '')
                                    <label for="item_description"
                                           class="input-group-addon ig-addon-aligned">@lang('description')</label>
                                    <textarea name="item_description" id="item_description"
                                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"></textarea>
                                @else
                                    <label for="item_date"
                                           class="input-group-addon ig-addon-aligned">@lang('date')</label>
                                    <input type="text" name="item_date" id="item_date"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors datepicker"
                                           value="{{ format_date(date('Y-m-d')) }}"{{ $invoice_disabled }}>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full px-4 col-sm-5 md:w-1/2 col-lg-7">
                    <div class="flex flex-wrap -mx-4">
                        <div class="w-full px-4 lg:w-1/2">
                            <div class="input-group">
                                <label for="item_quantity"
                                       class="input-group-addon ig-addon-aligned">@lang('quantity')</label>
                                <input type="text" name="item_quantity" id="item_quantity"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="">
                            </div>

                            <div class="input-group">
                                <label for="item_product_unit_id"
                                       class="input-group-addon ig-addon-aligned">@lang('product_unit')</label>
                                <select name="item_product_unit_id" id="item_product_unit_id"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors">
                                    <option value="0">@lang('none')</option>
                                    @foreach($units as $unit)
                                        <option
                                            value="{{ $unit->unit_id }}">{{ $unit->unit_name . '/' . $unit->unit_name_plrl }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group">
                                <label for="item_price"
                                       class="input-group-addon ig-addon-aligned">@lang('price')</label>
                                <input type="text" name="item_price" id="item_price"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="">
                                <div class="input-group-addon">{{ get_setting('currency_symbol') }}</div>
                            </div>

                            @if(!$legacy_calculation)
                                @php $this->layout->loadView('layout/partial/itemlist_responsive_item_discount_input'); @endphp
                            @endif

                            <div class="input-group">
                                <label for="item_tax_rate_id"
                                       class="input-group-addon ig-addon-aligned">@lang('tax_rate')</label>
                                <select name="item_tax_rate_id" id="item_tax_rate_id"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors">
                                    <option value="0">@lang('none')</option>
                                    @foreach($tax_rates as $tax_rate)
                                        <option
                                            value="{{ $tax_rate->tax_rate_id }}" @php check_select(get_setting('default_item_tax_rate'), $tax_rate->tax_rate_id); @endphp>
                                            {{ format_amount($tax_rate->tax_rate_percent) . '% - ' . $tax_rate->tax_rate_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if($legacy_calculation)
                                @php $this->layout->loadView('layout/partial/itemlist_responsive_item_discount_input'); @endphp
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loop over existing items --}}
        @foreach($items as $item)
            @include('invoices.partials.item_row', ['item' => $item, 'invoice_disabled' => $invoice_disabled, 'units' => $units, 'tax_rates' => $tax_rates])
        @endforeach
    </div>
</div>
