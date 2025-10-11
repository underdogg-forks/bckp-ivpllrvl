$invoice_disabled = $invoice->is_read_only != 1 ? '' : ' disabled="disabled"';
@endphp

<div class="flex flex-wrap -mx-4">
    <div id="item_table" class="items table w-full px-4">
        <div id="new_row" class="mb-4 details-box" style="display: none;">
            <div class="flex flex-wrap -mx-4">
                <div class="w-full px-4 col-sm-7 md:w-1/2 col-lg-5">
                    <div class="flex flex-wrap -mx-4">
                        <div class="w-full px-4 col-sm-1">
                            <button type="button" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors up" title="@lang('move_up')">
                                <i class="fa fa-chevron-up"></i>
                            </button>
                            <button type="button" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors down" title="@lang('move_down')">
                                <i class="fa fa-chevron-down"></i>
                            </button>
                            @if($invoice->invoice_is_recurring)
                            <i title="{{ trans('recurring') }}"
                               class="js-item-recurrence-toggler cursor-pointer fa fa-calendar-o text-muted"></i>
                            <input type="hidden" name="item_is_recurring" value=""/>
                            @endif
                            <button type="button" class="btn_delete_item inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5" title="@lang('delete')">
                                <i class="fa fa-trash-o text-danger"></i>
                            </button>
                        </div>

                        <div class="w-full px-4 col-sm-11">
                            <div class="input-group">
                                <label for="item_name" class="input-group-addon ig-addon-aligned">@lang('item')</label>
                                <input type="text" name="item_name" id="item_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors" value="">
                            </div>
                            <div class="input-group">
                                @if($invoice->sumex_id == '')
                                <label for="item_description"
                                       class="input-group-addon ig-addon-aligned">@lang('description')</label>
                                <textarea name="item_description" id="item_description" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"></textarea>
                                @else
                                <label for="item_date" class="input-group-addon ig-addon-aligned">@lang('date')</label>
                                <input type="text" name="item_date" id="item_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors datepicker"
                                       value="{{ format_date(date('y-m-d')) }}"{{ $invoice_disabled }}>
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
                                <input type="text" name="item_quantity" id="item_quantity" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="">
                            </div>
                            <div class="input-group">
                                <label for="item_product_unit_id"
                                       class="input-group-addon ig-addon-aligned">@lang('product_unit')</label>
                                <select name="item_product_unit_id" id="item_product_unit_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors">
                                    <option value="0">@lang('none')</option>
                                    @foreach($units as $unit)
                                    <option value="{{ $unit->unit_id " }}>
                                        {{ $unit->unit_name . '/' . $unit->unit_name_plrl }}
                                    </option>@endforeach
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="item_price"
                                       class="input-group-addon ig-addon-aligned">@lang('price')</label>
                                <input type="text" name="item_price" id="item_price" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors" value="">
                                <div class="input-group-addon">{{ get_setting('currency_symbol') }}</div>
                            </div>
                            @if(!$legacy_calculation) {
    $this->layout->loadView('layout/partial/itemlist_responsive_item_discount_input');
}
                            <div class="input-group">
                                <label for="item_tax_rate_id"
                                       class="input-group-addon ig-addon-aligned">@lang('tax_rate')</label>
                                <select name="item_tax_rate_id" id="item_tax_rate_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors">
                                    <option value="0">@lang('none')</option>
                                    @foreach($tax_rates as $tax_rate)
                                    <option value="{{ $tax_rate->tax_rate_id }}"
                                        @php
                                            check_select(get_setting('default_item_tax_rate'), $tax_rate->tax_rate_id)>
                                        {{ format_amount($tax_rate->tax_rate_percent) . '% - ' . $tax_rate->tax_rate_name }}
                                    </option>@endforeach
                                </select>
                            </div>
                            @if($legacy_calculation) {
    $this->layout->loadView('layout/partial/itemlist_responsive_item_discount_input');
}
                        </div>

                        <input type="hidden" name="invoice_id" value="{{ $invoice_id " }}>
                        <input type="hidden" name="item_id" value="">
                        <input type="hidden" name="item_product_id" value="">
                        <input type="hidden" name="item_task_id" class="item-task-id" value="">

                        <div class="w-full px-4 md:w-1/2 text-right">
                            <div class="flex flex-wrap -mx-4 mb-1">
                                <div class="col-xs-9 col-sm-8">
                                    @lang('subtotal'):
                                </div>
                                <div class="w-1/4 px-4 col-sm-4">
                                    <span name="subtotal"></span>
                                </div>
                            </div>
                            @if(!$legacy_calculation) {
    $this->layout->loadView('layout/partial/itemlist_responsive_item_discount_show');
}
                            <div class="flex flex-wrap -mx-4 mb-1">
                                <div class="col-xs-9 col-sm-8">
                                    @lang('tax'):
                                </div>
                                <div class="w-1/4 px-4 col-sm-4">
                                    <span name="item_tax_total"></span>
                                </div>
                            </div>
                            @if($legacy_calculation) {
    $this->layout->loadView('layout/partial/itemlist_responsive_item_discount_show');
}
                            <div class="flex flex-wrap -mx-4 mb-1">
                                <strong>
                                    <div class="col-xs-9 col-sm-8">
                                        @lang('total'):
                                    </div>
                                    <div class="w-1/4 px-4 col-sm-4">
                                        <span name="item_total"></span>
                                    </div>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach($items as $item)
        <div class="mb-4 details-box item">
            <div class="flex flex-wrap -mx-4">
                <div class="w-full px-4 col-sm-7 md:w-1/2 col-lg-5">
                    <div class="flex flex-wrap -mx-4">
                        <div class="w-full px-4 col-sm-1">
                            <button type="button" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors up"
                                    title="@lang('move_up')"{{ $invoice_disabled }}>
                                <i class="fa fa-chevron-up"></i>
                            </button>
                            <button type="button" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors down"
                                    title="@lang('move_down')"{{ $invoice_disabled }}>
                                <i class="fa fa-chevron-down"></i>
                            </button>
                            @if($invoice->invoice_is_recurring) {
                                    @if($item->item_is_recurring == 1 || null === $item->item_is_recurring) {
                                        $item_recurrence_state = '1';
                                        $item_recurrence_class = 'fa-calendar-check-o text-success';
                                    } else {
                                        $item_recurrence_state = '0';
                                        $item_recurrence_class = 'fa-calendar-o text-muted';
                                    }

                            <i title="{{ trans('recurring') }}"
                               class="js-item-recurrence-toggler cursor-pointer fa {{ $item_recurrence_class" }}>
                            </i>
                            <input type="hidden" name="item_is_recurring" value="{{ $item_recurrence_state }}"/>
                            @php
                                }
                                @if($invoice->is_read_only != 1)
                            <button type="button" class="btn_delete_item inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" title="@lang('delete')"
                                    data-item-id="{{ $item->item_id " }}>
                                <i class="fa fa-trash-o text-danger"></i>
                            </button>@endforeach
                        </div>

                        <div class="w-full px-4 col-sm-11">
                            <input type="hidden" name="invoice_id" value="{{ $invoice_id " }}>
                            <input type="hidden" name="item_id" value="{{ $item->item_id }}"{{ $invoice_disabled }}>
                            <input type="hidden" name="item_task_id" class="item-task-id" value="@if($item->item_task_id)
{$item->item_task_id}@endforeach
    ">
                            <input type="hidden" name="item_product_id" value="{{ $item->item_product_id " }}>

                            <div class="input-group">
                                <label for="item_name_{{ $item->item_id }}"
                                       class="input-group-addon ig-addon-aligned">@lang('item')</label>
                                <input type="text" name="item_name" id="item_name_{{ $item->item_id }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ htmlspecialchars($item->item_name) }}"{{ $invoice_disabled }}>
                            </div>

                            <div class="input-group">
                                @if($invoice->sumex_id == '')
                                <label for="item_description_{{ $item->item_id }}"
                                       class="input-group-addon ig-addon-aligned">@lang('description')</label>
                                <textarea name="item_description" id="item_description_{{ $item->item_id }}"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"{{ $invoice_disabled }}>{!! $item->item_description !!}</textarea>
                                @else
                                <label for="item_date_{{ $item->item_id }}"
                                       class="input-group-addon ig-addon-aligned">@lang('date')</label>
                                <input type="text" name="item_date" id="item_date_{{ $item->item_id }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors datepicker"
                                       value="{{ format_date($item->item_date) }}"{{ $invoice_disabled }}>@endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full px-4 col-sm-5 md:w-1/2 col-lg-7">
                    <div class="flex flex-wrap -mx-4">
                        <div class="w-full px-4 lg:w-1/2">
                            <div class="input-group">
                                <label for="item_quantity_{{ $item->item_id }}"
                                       class="input-group-addon ig-addon-aligned">@lang('quantity')</label>
                                <input type="text" name="item_quantity" id="item_quantity_{{ $item->item_id }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ format_quantity($item->item_quantity) }}"{{ $invoice_disabled }}>
                            </div>
                            <div class="input-group">
                                <label for="item_product_unit_id_{{ $item->item_id }}"
                                       class="input-group-addon ig-addon-aligned">@lang('product_unit')</label>
                                <select name="item_product_unit_id" id="item_product_unit_id_{{ $item->item_id }}"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"{{ $invoice_disabled }}>
                                    <option value="0">@lang('none')</option>
                                    @foreach($units as $unit)
                                    <option value="{{ $unit->unit_id }}"
                                        @php
                                            check_select($item->item_product_unit_id, $unit->unit_id)>
                                        {!! $unit->unit_name) . '/' . htmlsc($unit->unit_name_plrl !!}
                                    </option>@endforeach
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="item_price_{{ $item->item_id }}"
                                       class="input-group-addon ig-addon-aligned">@lang('price')</label>
                                <input type="text" name="item_price" id="item_price_{{ $item->item_id }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ format_amount($item->item_price) }}"{{ $invoice_disabled }}>
                                <div class="input-group-addon">{{ get_setting('currency_symbol') }}</div>
                            </div>
                            @if(!$legacy_calculation) {
                                    $this->layout->loadView('layout/partial/itemlist_responsive_item_discount_input', ['item' => $item]);
                                }

                            <div class="input-group">
                                <label for="item_tax_rate_id_{{ $item->item_id }}"
                                       class="input-group-addon ig-addon-aligned">@lang('tax_rate')</label>
                                <select name="item_tax_rate_id" id="item_tax_rate_id_{{ $item->item_id }}"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"{{ $invoice_disabled }}>
                                    <option value="0">@lang('none')</option>
                                    @foreach($tax_rates as $tax_rate)
                                    <option value="{{ $tax_rate->tax_rate_id }}"
                                        @php
                                            check_select($item->item_tax_rate_id, $tax_rate->tax_rate_id)>
                                        {{ format_amount($tax_rate->tax_rate_percent) . '% - ' . $tax_rate->tax_rate_name }}
                                    </option>@endforeach
                                </select>
                            </div>
                            @if($legacy_calculation) {
                                    $this->layout->loadView('layout/partial/itemlist_responsive_item_discount_input', ['item' => $item]);
                                }

                        </div>
                        <div class="w-full px-4 md:w-1/2 text-right">
                            <div class="flex flex-wrap -mx-4 mb-1">
                                <div class="col-xs-9 col-sm-8">
                                    @lang('subtotal'):
                                </div>
                                <div class="w-1/4 px-4 col-sm-4">
                                    {{ format_currency($item->item_subtotal) }}
                                </div>
                            </div>
                            @if(!$legacy_calculation) {
                                    $this->layout->loadView('layout/partial/itemlist_responsive_item_discount_show', ['item' => $item]);
                                }

                            <div class="flex flex-wrap -mx-4 mb-1">
                                <div class="col-xs-9 col-sm-8">
                                    @lang('tax'):
                                </div>
                                <div class="w-1/4 px-4 col-sm-4">
                                    {{ format_currency($item->item_tax_total) }}
                                </div>
                            </div>
                            @if($legacy_calculation) {
                                    $this->layout->loadView('layout/partial/itemlist_responsive_item_discount_show', ['item' => $item]);
                                }

                            <div class="flex flex-wrap -mx-4 mb-1">
                                <div class="col-xs-9 col-sm-8">
                                    <b>@lang('total'):</b>
                                </div>
                                <div class="w-1/4 px-4 col-sm-4">
                                    <b>{{ format_currency($item->item_total) }}</b>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>@endforeach
    </div>
</div>

<br>

<div class="flex flex-wrap -mx-4">
    <div class="w-full px-4 md:w-1/3">
        <div class="inline-flex rounded-md shadow-sm">
            @if($invoice->is_read_only != 1)
            <a href="javascript:void(0);" class="btn_add_row inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fa fa-plus"></i> @lang('add_new_row')
            </a>
            <a href="javascript:void(0);" class="btn_add_product inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fa fa-database"></i>
                @lang('add_product')
            </a>
            <a href="javascript:void(0);"
               class="btn_add_task inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors{{ get_setting('projects_enabled') == 1 ? '' : ' hidden'" }}>
                <i class="fa fa-database"></i> @lang('add_task')
            </a>@endforeach
        </div>
    </div>

    <div class="w-full px-4 block sm:hidden hidden sm:block md:hidden"><br></div>

    <div class="w-full px-4 md:w-1/2 col-md-offset-2 lg:w-1/3 col-lg-offset-4">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700 text-right">
            @if(!$legacy_calculation) {
    $this->layout->loadView('invoices/partial_itemlist_table_invoice_discount');
}
            <tr>
                <td style="width: 40%;">@lang('subtotal')</td>
                <td style="width: 60%;"
                    class="amount">{{ format_currency($invoice->invoice_item_subtotal) }}</td>
            </tr>
            <tr>
                <td>@lang('item_tax')</td>
                <td class="amount">{{ format_currency($invoice->invoice_item_tax_total) }}</td>
            </tr>
            @if($legacy_calculation)
            <tr>
                <td>@lang('invoice_tax')</td>
                <td>
                    @if($invoice_tax_rates) {
                            @foreach($invoice_tax_rates as $invoice_tax_rate)
                    <form method="post"
                          action="{{ url('invoices/delete_invoice_tax/' . $invoice->invoice_id . '/' . $invoice_tax_rate->invoice_tax_rate_id) " }}>
                        @csrf
                        <span class="amount">
                        {{ format_currency($invoice_tax_rate->invoice_tax_rate_amount) }}
                    </span>
                        <span class="text-muted">
                        {!! $invoice_tax_rate->invoice_tax_rate_name) . ' ' . format_amount($invoice_tax_rate->invoice_tax_rate_percent !!}
                    </span>
                        <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                onclick="var Y=confirm('@lang('delete_tax_warning')');if(Y)show_loader();return Y;">
                            <i class="fa fa-trash-o"></i>
                        </button>
                    </form>
                    @php
                        }
                    } else {
                        echo format_currency('0');
                    }

                </td>
            </tr>
                <?php
                $this->layout->loadView('invoices/partial_itemlist_table_invoice_discount');
            }
            <tr>
                <td>{{ trans('total') }}</td>
                <td class="amount"><b>{{ format_currency($invoice->invoice_total) }}</b></td>
            </tr>
            <tr>
                <td>{{ trans('paid') }}</td>
                <td class="amount"><b>{{ format_currency($invoice->invoice_paid) }}</b></td>
            </tr>
            <tr>
                <td><b>{{ trans('balance') }}</b></td>
                <td class="amount"><b>{{ format_currency($invoice->invoice_balance) }}</b></td>
            </tr>
        </table>
    </div>

</div>
