$global_discount = $invoice->invoice_discount_percent > 0 ? format_amount($invoice->invoice_discount_percent) . '%' : format_currency($invoice->invoice_discount_amount);
@if($invoice_tax_rates) {
    $global_taxes = [];
    @foreach($invoice_tax_rates as $invoice_tax_rate) {
        $global_taxes[] = $invoice_tax_rate->invoice_tax_rate_name . ' (' . format_amount($invoice_tax_rate->invoice_tax_rate_percent) . '%): ' . format_currency($invoice_tax_rate->invoice_tax_rate_amount);
    }
    $global_taxes = implode('<br>', $global_taxes);
}
<div id="headerbar">
    <h1 class="headerbar-title">@lang('invoice') #{{ $invoice->invoice_number }}</h1>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
            @if($invoice->invoice_balance == 0 || $invoice->invoice_status_id >= 4)
            <button class="btn btn-success disabled">
                <i class="fa fa-check"></i> {{ __('paid') }}
            </button>
            @elseif($enable_online_payments)
            <a href="{{ url('guest/payment_information/form/' . $invoice->invoice_url_key) }}"
               class="btn btn-primary">
                <i class="fa fa-credit-card"></i>
                @lang('pay_now')
            </a>
            @endif
            <a href="{{ url('guest/invoices/generate_pdf/' . $invoice->invoice_id) }}"
               class="btn btn-default" id="btn_generate_pdf" target="_blank">
                <i class="fa fa-print"></i> @lang('download_pdf')
            </a>
        </div>

    </div>

</div>

<div id="content">

    {{ $this->layout->loadView('layout/alerts') }}

    <form id="invoice_form" class="form-horizontal">

        <div class="invoice">

            <div class="row">

                <div class="col-xs-12 col-md-9 clearfix">
                    <div class="pull-left">

                        <h3>{!! format_client($invoice) !!}</h3>

                        <div class="client-address">
                            @include('clients/partial_client_address', ['client' => $invoice])
                        </div>
                        @if($invoice->client_phone)
                        <br><span><strong>@lang('phone'):</strong> {!! $invoice->client_phone !!}</span>
                        @php
                            }
                            @if($invoice->client_email) {

                        <br><span><strong>@lang('email'):</strong> {!! $invoice->client_email !!}</span>
                        @endif
                    </div>
                </div>

                <div class="col-xs-12 col-md-3">

                    <table class="table table-bordered">
                        <tr>
                            <td>@lang('invoice') #</td>
                            <td>{{ $invoice->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td>@lang('date')</td>
                            <td>{{ date_from_mysql($invoice->invoice_date_created) }}</td>
                        </tr>
                        <tr class="{{ $invoice->invoice_status_id != 4 && $invoice->invoice_date_due < date('Y-m-d') ? 'font-overdue' : '' " }}>
                            <td>@lang('due_date')</td>
                            <td>{{ date_from_mysql($invoice->invoice_date_due) }}</td>
                        </tr>
                    </table>

                </div>

            </div>

            <br/>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th></th>
                        <th>@lang('item') / @lang('description')</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    @foreach($items as $i => $item)
                    <tbody class="item">
                    <tr>
                        <td rowspan="2" style="width:20px;" class="text-center">{{ 1 + $i }}</td>
                        <td>{!! $item->item_name !!}</td>
                        <td>
                            <span class="pull-left">@lang('quantity')</span>
                            <span
                                class="pull-right amount">{{ format_quantity($item->item_quantity) . ' ' . htmlsc($item->item_product_unit) }}</span>
                        </td>
                        <td>
                            <span class="pull-left">@lang('price')</span>
                            <span class="pull-right amount">{{ format_currency($item->item_price) }}</span>
                        </td>
                        <td>
                            <span class="pull-left">@lang('subtotal')</span>
                            <span class="pull-right amount">{{ format_currency($item->item_subtotal) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">{{ nl2br(e($item->item_description)) }}</td>
                        <td>
                            <span class="pull-left">@lang('discount')</span>
                            <span class="pull-right amount">
                                    <span data-toggle="tooltip" data-placement="bottom" title="@lang('item_discount')">
                                        {{ format_currency($item->item_discount) }}
                                    </span>
@php
    // New Discount calculation - since v1.6.3
    $item_global_discount = $legacy_calculation ? 0 : $item->item_subtotal - ($item->item_total - $item->item_tax_total + $item->item_discount);
    @if($item_global_discount) {

                                    <span data-toggle="tooltip" data-placement="bottom"
                                          title="@lang('global_discount')">
                                        + {{ format_currency($item_global_discount) }}
                                    </span>
                                    <span data-toggle="tooltip" data-placement="bottom"
                                          title="@lang('discount') (@lang('subtotal'))">
                                        = {{ format_currency($item_global_discount + $item->item_discount) }}
                                    </span>@endforeach
                                </span>
                        </td>
                        <td>
                            <span class="pull-left">@lang('tax')</span>
                            <span class="pull-right amount">{{ $item->item_tax_rate_percent ? $item->item_tax_rate_name . ' (' . format_amount($item->item_tax_rate_percent) . '%): ' : '';
    echo format_currency($item->item_tax_total) }}</span>
                        </td>
                        <td>
                            <span class="pull-left">@lang('total')</span>
                            <span class="pull-right amount">{{ format_currency($item->item_total) }}</span>
                        </td>
                    </tr>
                    </tbody>
                    @php
                        }
                        // End foreach items
                </table>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        @if(!$legacy_calculation)
                        <th class="text-right">@lang('global_discount')</th>@endforeach
                        <th class="text-right">@lang('subtotal')</th>
                        <th class="text-right">@lang('item_tax')</th>
                        @if($invoice_tax_rates)
                        <th class="text-right">@lang('invoice_tax')</th>
                        @php
                            }
                            @if($legacy_calculation) {

                        <th class="text-right">@lang('global_discount')</th>@endforeach
                        <th class="text-right">@lang('total')</th>
                        <th class="text-right">@lang('paid')</th>
                        <th class="text-right">@lang('balance')</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @if(!$legacy_calculation)
                        <td class="amount">{{ $global_discount }}</td>@endforeach
                        <td class="amount">{{ format_currency($invoice->invoice_item_subtotal) }}</td>
                        <td class="amount">{{ format_currency($invoice->invoice_item_tax_total) }}</td>
                        @if($invoice_tax_rates)
                        <td class="amount">{{ $global_taxes }}</td>
                        @php
                            }
                            @if($legacy_calculation) {

                        <td class="amount">{{ $global_discount }}</td>@endforeach
                        <td class="amount"><b>{{ format_currency($invoice->invoice_total) }}</b></td>
                        <td class="amount">{{ format_currency($invoice->invoice_paid) }}</td>
                        <td class="amount">{{ format_currency($invoice->invoice_balance) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-xs-12 col-md-6">

                @php _dropzone_html()

            </div>
            @if($invoice->invoice_terms)
            <div class="col-xs-12 col-md-6">
                <strong>@lang('invoice_terms')</strong><br/>
                {{ nl2br(e($invoice->invoice_terms)) }}
            </div>@endforeach
        </div>

    </form>

</div>
<?php
_dropzone_script($invoice->invoice_url_key, $invoice->client_id, 'guest/get', false);
