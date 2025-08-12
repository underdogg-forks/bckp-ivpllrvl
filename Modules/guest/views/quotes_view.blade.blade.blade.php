@php namespace Modules\Guest\Views;

$global_discount = $quote->quote_discount_percent > 0 ? format_amount($quote->quote_discount_percent) . '%' : format_currency($quote->quote_discount_amount);
if ($quote_tax_rates) {
    $global_taxes = [];
    foreach ($quote_tax_rates as $quote_tax_rate) {
        $global_taxes[] = $quote_tax_rate->quote_tax_rate_name . ' (' . format_amount($quote_tax_rate->quote_tax_rate_percent) . '%): ' . format_currency($quote_tax_rate->quote_tax_rate_amount);
    }
    $global_taxes = implode('<br>', $global_taxes);
} @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@@lang('quote') #{{ $quote->quote_number }}</h1>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
@php if (in_array($quote->quote_status_id, [2, 3])) {
    @endphp
            <a href="{{ url('guest/quotes/approve/' . $quote->quote_id) }}"
               class="btn btn-success">
                <i class="fa fa-check"></i>
                @@lang('approve_this_quote')
            </a>
            <a href="{{ url('guest/quotes/reject/' . $quote->quote_id) }}"
               class="btn btn-danger">
                <i class="fa fa-times-circle"></i>
                @@lang('reject_this_quote')
            </a>
@php
} elseif ($quote->quote_status_id == 4) {
    @endphp
            <a href="#" class="btn btn-success disabled">
                <i class="fa fa-check"></i>
                @@lang('quote_approved')
            </a>
@php
} elseif ($quote->quote_status_id == 5) {
    @endphp
            <a href="#" class="btn btn-danger disabled">
                <i class="fa fa-times-circle"></i>
                @@lang('quote_rejected')
            </a>
@php
} @endphp
            <a href="{{ url('guest/quotes/generate_pdf/' . $quote_id) }}"
               class="btn btn-default" id="btn_generate_pdf" target="_blank">
                <i class="fa fa-print"></i> @@lang('download_pdf')
            </a>
        </div>

    </div>

</div>

<div id="content">

    {{ $this->layout->loadView('layout/alerts') }}

    <div class="quote">

        <div class="row">

            <div class="col-xs-12 col-md-9 clearfix">
                <div class="pull-left">

                    <h3>@php _htmlsc(format_client($quote)); @endphp</h3>
                    <div class="client-address">
                        @php $this->layout->loadView('clients/partial_client_address', ['client' => $quote]); @endphp
                    </div>
@php if ($quote->client_phone) {
    @endphp
                    <br><span><strong>@@lang('phone'):</strong> @php
    _htmlsc($quote->client_phone);
    @endphp</span>
@php
}
if ($quote->client_email) {
    @endphp
                    <br><span><strong>@@lang('email'):</strong> @php
    _htmlsc($quote->client_email);
    @endphp</span>
@php
} @endphp
                </div>
            </div>

            <div class="col-xs-12 col-md-3">

                <table class="table table-bordered">
                    <tr>
                        <td>@@lang('quote') #</td>
                        <td>{{ $quote->quote_number }}</td>
                    </tr>
                    <tr>
                        <td>@@lang('date')</td>
                        <td>{{ date_from_mysql($quote->quote_date_created) }}</td>
                    </tr>
                    <tr>
                        <td>@@lang('due_date')</td>
                        <td>{{ date_from_mysql($quote->quote_date_expires) }}</td>
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
                    <th>@@lang('item') / {{ @@lang('description') }}</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
@php foreach ($items as $i => $item) {
    @endphp
                <tbody class="item">
                <tr>
                    <td rowspan="2" style="width:20px;" class="text-center">{{ 1 + $i }}</td>
                    <td>@php
    _htmlsc($item->item_name);
    @endphp</td>
                    <td>
                        <span class="pull-left">@@lang('quantity')</span>
                        <span class="pull-right amount">{{ format_quantity($item->item_quantity) . ' ' . htmlsc($item->item_product_unit) }}</span>
                    </td>
                    <td>
                        <span class="pull-left">@@lang('price')</span>
                        <span class="pull-right amount">{{ format_currency($item->item_price) }}</span>
                    </td>
                    <td>
                        <span class="pull-left">@@lang('subtotal')</span>
                        <span class="pull-right amount">{{ format_currency($item->item_subtotal) }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">{{ nl2br(htmlsc($item->item_description)) }}</td>
                    <td>
                        <span class="pull-left">@@lang('discount')</span>
                        <span class="pull-right amount">
                            <span data-toggle="tooltip" data-placement="bottom" title="@@lang('item_discount')">
                                {{ format_currency($item->item_discount) }}
                            </span>
@php
    // New Discount calculation - since v1.6.3
    $item_global_discount = $legacy_calculation ? 0 : $item->item_subtotal - ($item->item_total - $item->item_tax_total + $item->item_discount);
    if ($item_global_discount) {
        @endphp
                            <span data-toggle="tooltip" data-placement="bottom" title="@@lang('global_discount')">
                                + {{ format_currency($item_global_discount) }}
                            </span>
                            <span data-toggle="tooltip" data-placement="bottom" title="@@lang('discount') (@@lang('subtotal'))">
                                = {{ format_currency($item_global_discount + $item->item_discount) }}
                            </span>
@php
    }
    @endphp
                        </span>
                    </td>
                    <td>
                        <span class="pull-left">@@lang('tax')</span>
                        <span class="pull-right amount">{{ $item->item_tax_rate_percent ? $item->item_tax_rate_name . ' (' . format_amount($item->item_tax_rate_percent) . '%): ' : '';
    echo format_currency($item->item_tax_total) }}</span>
                    </td>
                    <td>
                        <span class="pull-left">@@lang('total')</span>
                        <span class="pull-right amount">{{ format_currency($item->item_total) }}</span>
                    </td>
                </tr>
                </tbody>
@php
}
// End foreach items @endphp
            </table>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
@php if (!$legacy_calculation) {
    @endphp
                    <th class="text-right">@@lang('global_discount')</th>
@php
} @endphp
                    <th class="text-right">@@lang('subtotal')</th>
                    <th class="text-right">@@lang('item_tax')</th>
@php if ($quote_tax_rates) {
    @endphp
                    <th class="text-right">@@lang('quote_tax')</th>
@php
}
if ($legacy_calculation) {
    @endphp
                    <th class="text-right">@@lang('global_discount')</th>
@php
} @endphp
                    <th class="text-right">@@lang('total')</th>
                </tr>
                </thead>
                <tbody>
                <tr>
@php if (!$legacy_calculation) {
    @endphp
                    <td class="amount">{{ $global_discount }}</td>
@php
} @endphp
                    <td class="amount">{{ format_currency($quote->quote_item_subtotal) }}</td>
                    <td class="amount">{{ format_currency($quote->quote_item_tax_total) }}</td>
@php if ($quote_tax_rates) {
    @endphp
                    <td class="amount">{{ $global_taxes }}</td>
@php
}
if ($legacy_calculation) {
    @endphp
                    <td class="amount">{{ $global_discount }}</td>
<?php
} @endphp
                    <td class="amount"><b>{{ format_currency($quote->quote_total) }}</b></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-xs-12 col-md-6">

        @php _dropzone_html(); @endphp

    </div>
</div>

<?php
_dropzone_script($quote->quote_url_key, $quote->client_id, 'guest/get', false);