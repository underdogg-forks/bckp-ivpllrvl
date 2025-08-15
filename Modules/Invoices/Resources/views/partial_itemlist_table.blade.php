@php namespace Modules\Invoices\Views;

$invoice_disabled = $invoice->is_read_only != 1 ? '' : ' disabled="disabled"'; @endphp
<div class="table-responsive">
    <table id="item_table" class="items table table-condensed table-bordered no-margin">

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
            <td rowspan="2" class="td-icon">
                <i class="fa fa-arrows cursor-move"></i>
                @if($invoice->invoice_is_recurring)
                <br/>
                <i title="@lang('recurring')"
                   class="js-item-recurrence-toggler cursor-pointer fa fa-calendar-o text-muted"></i>
                <input type="hidden" name="item_is_recurring" value=""/>
                @endif
            </td>
            <td class="td-text">
                <input type="hidden" name="invoice_id" value="{{ $invoice_id }}">
                <input type="hidden" name="item_id" value="">
                <input type="hidden" name="item_product_id" value="">
                <input type="hidden" name="item_task_id" class="item-task-id" value="">

                <div class="input-group">
                    <span class="input-group-addon">@lang('item')</span>
                    <input type="text" name="item_name" class="form-control" value="">
                </div>
            </td>
            <td class="td-amount td-quantity">
                <div class="input-group">
                    <span class="input-group-addon">@lang('quantity')</span>
                    <input type="text" name="item_quantity" class="form-control amount" value="">
                </div>
            </td>
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon">@lang('price')</span>
                    <input type="text" name="item_price" class="form-control amount" value="">
                    <div class="input-group-addon">{{ get_setting('currency_symbol') }}</div>
                </div>
            </td>
            @if(!$legacy_calculation) {
    $this->layout->loadView('layout/partial/itemlist_table_item_discount_input');
} @endphp
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon">@lang('tax_rate')</span>
                    <select name="item_tax_rate_id" class="form-control">
                        <option value="0">@lang('none')</option>
                        @foreach($tax_rates as $tax_rate)
                        <option value="{{ $tax_rate->tax_rate_id }}"
                            @php
                                check_select(get_setting('default_item_tax_rate'), $tax_rate->tax_rate_id);
                            @endphp>
                            {{ format_amount($tax_rate->tax_rate_percent) . '% - ' . $tax_rate->tax_rate_name }}
                        </option>
                        @endif
                    </select>
                </div>
            </td>
            @if($legacy_calculation) {
    $this->layout->loadView('layout/partial/itemlist_table_item_discount_input');
} @endphp
            <td class="td-icon text-right td-vert-middle">
                <button type="button" class="btn_delete_item btn btn-link btn-sm" title="@lang('delete')">
                    <i class="fa fa-trash-o text-danger"></i>
                </button>
            </td>
        </tr>
        <tr>
            @if($invoice->sumex_id == '')
            <td class="td-textarea">
                <div class="input-group">
                    <span class="input-group-addon">@lang('description')</span>
                    <textarea name="item_description" class="form-control"></textarea>
                </div>
            </td>
            @else
            <td class="td-date">
                <div class="input-group">
                    <span class="input-group-addon">@lang('date')</span>
                    <input type="text" name="item_date" class="form-control datepicker"
                           value="{{ format_date(date('y-m-d')) }}"{{ $invoice_disabled }}>
                </div>
            </td>
            @endif
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon">@lang('product_unit')</span>
                    <select name="item_product_unit_id" class="form-control">
                        <option value="0">@lang('none')</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->unit_id }}">
                            {{ $unit->unit_name . '/' . $unit->unit_name_plrl }}
                        </option>
                        @endif
                    </select>
                </div>
            </td>
            <td class="td-amount td-vert-middle">
                <span>@lang('subtotal')</span><br/>
                <span name="subtotal" class="amount"></span>
            </td>
            @if(!$legacy_calculation) {
    $this->layout->loadView('layout/partial/itemlist_table_item_discount_show');
} @endphp
            <td class="td-amount td-vert-middle">
                <span>@lang('tax')</span><br/>
                <span name="item_tax_total" class="amount"></span>
            </td>
            @if($legacy_calculation) {
    $this->layout->loadView('layout/partial/itemlist_table_item_discount_show');
} @endphp
            <td class="td-amount td-vert-middle">
                <span>@lang('total')</span><br/>
                <span name="item_total" class="amount"></span>
            </td>
        </tr>
        </tbody>

        @foreach($items as $item)
        <tbody class="item">
        <tr>
            <td rowspan="2" class="td-icon">
                <i class="fa fa-arrows cursor-move"></i>
                @if($invoice->invoice_is_recurring) {
                        if ($item->item_is_recurring == 1 || null === $item->item_is_recurring) {
                            $item_recurrence_state = '1';
                            $item_recurrence_class = 'fa-calendar-check-o text-success';
                        } else {
                            $item_recurrence_state = '0';
                            $item_recurrence_class = 'fa-calendar-o text-muted';
                        }
                @endphp
                <br/>
                <i title="{{ trans('recurring') }}"
                   class="js-item-recurrence-toggler cursor-pointer fa {{ $item_recurrence_class }}"></i>
                <input type="hidden" name="item_is_recurring" value="{{ $item_recurrence_state }}"/>
                @endif
            </td>
            <td class="td-text">
                <input type="hidden" name="invoice_id" value="{{ $invoice_id }}">
                <input type="hidden" name="item_id" value="{{ $item->item_id }}"{{ $invoice_disabled }}>
                <input type="hidden" name="item_task_id" class="item-task-id"
                       value="{{ $item->item_task_id ? $item->item_task_id : '' }}">
                <input type="hidden" name="item_product_id" value="{{ $item->item_product_id }}">

                <div class="input-group">
                    <span class="input-group-addon">@lang('item')</span>
                    <input type="text" name="item_name" class="form-control"
                           value="{!! $item->item_name !!}"{{ $invoice_disabled }}>
                </div>
            </td>
            <td class="td-amount td-quantity">
                <div class="input-group">
                    <span class="input-group-addon">@lang('quantity')</span>
                    <input type="text" name="item_quantity" class="form-control amount"
                           value="{{ format_quantity($item->item_quantity) }}"{{ $invoice_disabled }}>
                </div>
            </td>
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon">@lang('price')</span>
                    <input type="text" name="item_price" class="form-control amount"
                           value="{{ format_amount($item->item_price) }}"{{ $invoice_disabled }}>
                    <div class="input-group-addon">{{ get_setting('currency_symbol') }}</div>
                </div>
            </td>
            @if(!$legacy_calculation) {
                    $this->layout->loadView('layout/partial/itemlist_table_item_discount_input', ['item' => $item]);
                }
            @endphp
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon">@lang('tax_rate')</span>
                    <select name="item_tax_rate_id" class="form-control"{{ $invoice_disabled }}>
                        <option value="0">@lang('none')</option>
                        @foreach($tax_rates as $tax_rate)
                        <option value="{{ $tax_rate->tax_rate_id }}"
                            @php
                                check_select($item->item_tax_rate_id, $tax_rate->tax_rate_id);
                            @endphp>
                            {{ format_amount($tax_rate->tax_rate_percent) . '% - ' . $tax_rate->tax_rate_name }}
                        </option>
                        @endif
                    </select>
                </div>
            </td>
            @if($legacy_calculation) {
                    $this->layout->loadView('layout/partial/itemlist_table_item_discount_input', ['item' => $item]);
                }
            @endphp
            <td class="td-icon text-right td-vert-middle">
                @if($invoice->is_read_only != 1)
                <button type="button" class="btn_delete_item btn btn-link btn-sm" title="@lang('delete')"
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
                    <textarea name="item_description" class="form-control"{{ $invoice_disabled }}
                            >{!! $item->item_description !!}</textarea>
                </div>
            </td>
            @else
            <td class="td-date">
                <div class="input-group">
                    <span class="input-group-addon">@lang('date')</span>
                    <input type="text" name="item_date" class="form-control datepicker"
                           value="{{ format_date($item->item_date) }}"{{ $invoice_disabled }}>
                </div>
            </td>
            @endif

            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon">@lang('product_unit')</span>
                    <select name="item_product_unit_id" class="form-control">
                        <option value="0">@lang('none')</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->unit_id }}"
                            @php
                                check_select($item->item_product_unit_id, $unit->unit_id);
                            @endphp>
                            {!! $unit->unit_name) . '/' . htmlsc($unit->unit_name_plrl !!}
                        </option>
                        @endif
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
            @endphp
            <td class="td-amount td-vert-middle">
                <span>@lang('tax')</span><br/>
                <span name="item_tax_total" class="amount">
                        {{ format_currency($item->item_tax_total) }}
                    </span>
            </td>
            @if($legacy_calculation) {
                    $this->layout->loadView('layout/partial/itemlist_table_item_discount_show', ['item' => $item]);
                }
            @endphp
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
            // End foreach items @endphp

    </table>
</div>

<br>

<div class="row">
    <div class="col-xs-12 col-md-4">
        <div class="btn-group">
            @if($invoice->is_read_only != 1)
            <a href="javascript:void(0);" class="btn_add_row btn btn-sm btn-default">
                <i class="fa fa-plus"></i> @lang('add_new_row')
            </a>
            <a href="javascript:void(0);" class="btn_add_product btn btn-sm btn-default">
                <i class="fa fa-database"></i>
                @lang('add_product')
            </a>
            <a href="javascript:void(0);"
               class="btn_add_task btn btn-sm btn-default{{ get_setting('projects_enabled') == 1 ? '' : ' hidden' }}">
                <i class="fa fa-database"></i> @lang('add_task')
            </a>
            @endif
        </div>
    </div>

    <div class="col-xs-12 visible-xs visible-sm"><br></div>

    <div class="col-xs-12 col-md-6 col-md-offset-2 col-lg-4 col-lg-offset-4">
        <table class="table table-bordered text-right">
            @if(!$legacy_calculation) {
    $this->layout->loadView('invoices/partial_itemlist_table_invoice_discount');
} @endphp
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
                            foreach ($invoice_tax_rates as $invoice_tax_rate)
                    <form method="post"
                          action="{{ url('invoices/delete_invoice_tax/' . $invoice->invoice_id . '/' . $invoice_tax_rate->invoice_tax_rate_id) }}">
                        @csrf
                        <button type="submit" class="btn btn-xs btn-link"
                                onclick="var Y=confirm('@lang('delete_tax_warning')');if(Y)show_loader();return Y;">
                            <i class="fa fa-trash-o"></i>
                        </button>
                        <span class="text-muted">
                            {!! $invoice_tax_rate->invoice_tax_rate_name) . ' ' . format_amount($invoice_tax_rate->invoice_tax_rate_percent) . '%' }}
                        </span>
                        <span class="amount">
                            {{ format_currency($invoice_tax_rate->invoice_tax_rate_amount !!}
                        </span>
                    </form>
                    @php
                        }
                    } else {
                        echo format_currency('0');
                    }
                    @endphp
                </td>
            </tr>
                <?php
                $this->layout->loadView('invoices/partial_itemlist_table_invoice_discount');
            } @endphp
            <tr>
                <td>@lang('total')</td>
                <td class="amount"><b>{{ format_currency($invoice->invoice_total) }}</b></td>
            </tr>
            <tr>
                <td>@lang('paid')</td>
                <td class="amount"><b>{{ format_currency($invoice->invoice_paid) }}</b></td>
            </tr>
            <tr>
                <td><b>@lang('balance')</b></td>
                <td class="amount"><b>{{ format_currency($invoice->invoice_balance) }}</b></td>
            </tr>
        </table>
    </div>
</div>
<?php
