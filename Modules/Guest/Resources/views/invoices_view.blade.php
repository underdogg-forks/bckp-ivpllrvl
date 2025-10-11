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

    <div class="headerbar-item float-right">
        <div class="inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm">
            @if($invoice->invoice_balance == 0 || $invoice->invoice_status_id >= 4)
            <button class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors disabled">
                <i class="fa fa-check"></i> {{ __('paid') }}
            </button>
            @elseif($enable_online_payments)
            <a href="{{ url('guest/payment_information/form/' . $invoice->invoice_url_key) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fa fa-credit-card"></i>
                @lang('pay_now')
            </a>
            @endif
            <a href="{{ url('guest/invoices/generate_pdf/' . $invoice->invoice_id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" id="btn_generate_pdf" target="_blank">
                <i class="fa fa-print"></i> @lang('download_pdf')
            </a>
        </div>

    </div>

</div>

<div id="content">

    {{ $this->layout->loadView('layout/alerts') }}

    <form id="invoice_form" class="space-y-4">

        <div class="invoice">

            <div class="flex flex-wrap -mx-4">

                <div class="w-full px-4 col-md-9 clear-both">
                    <div class="float-left">

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

                <div class="w-full px-4 md:w-1/4">

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700">
                        <tr>
                            <td>@lang('invoice') #</td>
                            <td>{{ $invoice->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td>@lang('date')</td>
                            <td>{{ date_from_mysql($invoice->invoice_date_created) }}</td>
                        </tr>
                        <tr class="{{ $invoice->invoice_status_id != 4 && $invoice->invoice_date_due < date('Y-m-d') ? 'font-overdue' : ''" }}>
                            <td>@lang('due_date')</td>
                            <td>{{ date_from_mysql($invoice->invoice_date_due) }}</td>
                        </tr>
                    </table>

                </div>

            </div>

            <br/>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700">
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
                            <span class="float-left">@lang('quantity')</span>
                            <span
                                class="float-right amount">{{ format_quantity($item->item_quantity) . ' ' . htmlsc($item->item_product_unit) }}</span>
                        </td>
                        <td>
                            <span class="float-left">@lang('price')</span>
                            <span class="float-right amount">{{ format_currency($item->item_price) }}</span>
                        </td>
                        <td>
                            <span class="float-left">@lang('subtotal')</span>
                            <span class="float-right amount">{{ format_currency($item->item_subtotal) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">{{ nl2br(e($item->item_description)) }}</td>
                        <td>
                            <span class="float-left">@lang('discount')</span>
                            <span class="float-right amount">
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
                            <span class="float-left">@lang('tax')</span>
                            <span class="float-right amount">{{ $item->item_tax_rate_percent ? $item->item_tax_rate_name . ' (' . format_amount($item->item_tax_rate_percent) . '%): ' : '';
    echo format_currency($item->item_tax_total) }}</span>
                        </td>
                        <td>
                            <span class="float-left">@lang('total')</span>
                            <span class="float-right amount">{{ format_currency($item->item_total) }}</span>
                        </td>
                    </tr>
                    </tbody>
                    @php
                        }
                        // End foreach items
                </table>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700">
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

            <div class="w-full px-4 md:w-1/2">

                @php _dropzone_html()

            </div>
            @if($invoice->invoice_terms)
            <div class="w-full px-4 md:w-1/2">
                <strong>@lang('invoice_terms')</strong><br/>
                {{ nl2br(e($invoice->invoice_terms)) }}
            </div>@endforeach
        </div>

    </form>

</div>
<?php
_dropzone_script($invoice->invoice_url_key, $invoice->client_id, 'guest/get', false);
