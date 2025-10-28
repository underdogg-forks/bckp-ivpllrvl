@php
    $add_table_and_head_for_sums = 1;

    $colspan = $show_item_discounts ? 5 : 4;
    $text_class = '';
    $text_class_date = '';
    $text_class_balance = '';
    $watermark = '';
    $stamp = '';
    $show_qrcode = $invoice->invoice_balance > 0 && $invoice->invoice_balance < 10e9 && get_setting('qr_code');
    $invoice_mode ??= 'default';

    switch ($invoice_mode) {
        case 'overdue':
            $text_class = 'text-red';
            $text_class_date = ' class="text-red"';
            $text_class_balance = ' class="text-red"';
            $watermark = '<watermarktext content="' . trans('overdue') . '" alpha="0.2" />';
            $stamp = '<span class="stamp overdue">' . trans('overdue') . '</span>';
            break;

        case 'paid':
            $show_qrcode = false;
            $text_class = 'text-green';
            $text_class_balance = ' class="text-green"';
            $watermark = '<watermarktext content="' . trans('paid') . '" alpha="0.2" />';
            $stamp = '<span class="stamp paid">' . trans('paid') . '</span>';
            break;

        default:
            break;
    }
@endphp

    <!DOCTYPE html>
<html lang="@lang('cldr')">
<head>
    <meta charset="utf-8">
    <title>{{ get_setting('custom_title', 'InvoicePlane', true) }} - @lang('invoice')</title>
    <link rel="stylesheet" href="{{ _theme_asset('css/templates.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ _core_asset('css/custom-pdf.css') }}" type="text/css">
</head>
<body>
<header class="clear-both">
    <div id="logo">
        {!! invoice_logo_pdf() !!}
    </div>

    <div id="client">
        <b>{!! format_client($invoice) !!}</b>

        @if($invoice->client_vat_id)
            <div>{{ trans('vat_id_short') }}: {{ htmlsc($invoice->client_vat_id) }}</div>
        @endif
        @if($invoice->client_tax_code)
            <div>{{ trans('tax_code_short') }}: {{ htmlsc($invoice->client_tax_code) }}</div>
        @endif
        @if($invoice->client_address_1)
            <div>{{ htmlsc($invoice->client_address_1) }}</div>
        @endif
        @if($invoice->client_address_2)
            <div>{{ htmlsc($invoice->client_address_2) }}</div>
        @endif
        @if($invoice->client_city || $invoice->client_state || $invoice->client_zip)
            <div>
                {{ htmlsc($invoice->client_city) }}
                {{ htmlsc($invoice->client_state) }}
                {{ htmlsc($invoice->client_zip) }}
            </div>
        @endif
        @if($invoice->client_country)
            <div>{{ get_country_name(trans('cldr'), htmlsc($invoice->client_country)) }}</div>
        @endif
        @if($invoice->client_phone)
            <div>{{ trans('phone_abbr') }}: {{ htmlsc($invoice->client_phone) }}</div>
        @endif
    </div>

    <div id="company">
        <b>{!! $invoice->user_name !!}</b>

        @if($invoice->user_vat_id)
            <div>{{ trans('vat_id_short') }}: {{ htmlsc($invoice->user_vat_id) }}</div>
        @endif
        @if($invoice->user_tax_code)
            <div>{{ trans('tax_code_short') }}: {{ htmlsc($invoice->user_tax_code) }}</div>
        @endif
        @if($invoice->user_address_1)
            <div>{{ htmlsc($invoice->user_address_1) }}</div>
        @endif
        @if($invoice->user_address_2)
            <div>{{ htmlsc($invoice->user_address_2) }}</div>
        @endif
        @if($invoice->user_city || $invoice->user_state || $invoice->user_zip)
            <div>
                {{ htmlsc($invoice->user_city) }}
                {{ htmlsc($invoice->user_state) }}
                {{ htmlsc($invoice->user_zip) }}
            </div>
        @endif
        @if($invoice->user_country)
            <div>{{ get_country_name(trans('cldr'), htmlsc($invoice->user_country)) }}</div>
        @endif
        @if($invoice->user_phone)
            <div>{{ trans('phone_abbr') }}: {{ htmlsc($invoice->user_phone) }}</div>
        @endif
        @if($invoice->user_fax)
            <div>{{ trans('fax_abbr') }}: {{ htmlsc($invoice->user_fax) }}</div>
        @endif
    </div>
</header>

{!! $watermark !!}

<main>
    <div class="invoice-details clear-both">
        <table class="large">
            <tr>
                <td rowspan="{{ $payment_method ? 5 : 4 }}" style="width:40%;text-align:left;">{!! $stamp !!}</td>
            </tr>
            <tr>
                <td>@lang('invoice_date'):</td>
                <td>{{ date_from_mysql($invoice->invoice_date_created, true) }}</td>
            </tr>
            <tr>
                <td{!! $text_class_date !!}>@lang('due_date'):</td>
                <td{!! $text_class_date !!}>{{ date_from_mysql($invoice->invoice_date_due, true) }}</td>
            </tr>
            <tr>
                <td{!! $text_class_balance !!}>@lang('amount_due'):</td>
                <td{!! $text_class_balance !!}>{{ format_currency($invoice->invoice_balance) }}</td>
            </tr>
            @if($payment_method)
                <tr>
                    <td>@lang('payment_method'):</td>
                    <td>{!! $payment_method->payment_method_name !!}</td>
                </tr>
            @endif
        </table>
    </div>

    <h1 class="invoice-title {{ $text_class }}">@lang('invoice') {!! $invoice->invoice_number !!}</h1>

    <table class="item-table">
        <thead>
        <tr>
            <th class="item-name">@lang('item')</th>
            <th class="item-desc">@lang('description')</th>
            <th class="item-amount text-right">@lang('qty')</th>
            <th class="item-price text-right">@lang('price')</th>
            @if($show_item_discounts)
                <th class="item-discount text-right">@lang('discount')</th>
            @endif
            <th class="item-total text-right">@lang('total')</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>{!! $item->item_name !!}</td>
                <td>{{ nl2br(e($item->item_description)) }}</td>
                <td class="text-right">
                    {{ format_quantity($item->item_quantity) }}
                    @if($item->item_product_unit)
                        <br><small>{!! $item->item_product_unit !!}</small>
                    @endif
                </td>
                <td class="text-right">{{ format_currency($item->item_price) }}</td>
                @if($show_item_discounts)
                    <td class="text-right">{{ format_currency($item->item_discount) }}</td>
                @endif
                <td class="text-right">{{ format_currency($item->item_total) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if($add_table_and_head_for_sums)
        <table class="item-table">
            <thead>
            <tr>
                <th colspan="{{ $colspan }}">&nbsp;</th>
                <th class="text-right">@lang('total')</th>
            </tr>
            </thead>
            @endif

            <tbody class="invoice-sums">
            @if(! $legacy_calculation)
                {!! discount_global_print_in_pdf($invoice, $show_item_discounts) !!}
            @endif

            <tr>
                <td class="text-right" colspan="{{ $colspan }}">@lang('subtotal')</td>
                <td class="text-right">{{ format_currency($invoice->invoice_item_subtotal) }}</td>
            </tr>

            @if($invoice->invoice_item_tax_total > 0)
                <tr>
                    <td class="text-right" colspan="{{ $colspan }}">@lang('item_tax')</td>
                    <td class="text-right">{{ format_currency($invoice->invoice_item_tax_total) }}</td>
                </tr>
            @endif

            @foreach($invoice_tax_rates as $invoice_tax_rate)
                <tr>
                    <td class="text-right" colspan="{{ $colspan }}">
                        {!! $invoice_tax_rate->invoice_tax_rate_name !!}
                        ({{ format_amount($invoice_tax_rate->invoice_tax_rate_percent) }}%)
                    </td>
                    <td class="text-right">{{ format_currency($invoice_tax_rate->invoice_tax_rate_amount) }}</td>
                </tr>
            @endforeach

            @if($legacy_calculation)
                {!! discount_global_print_in_pdf($invoice, $show_item_discounts) !!}
            @endif

            <tr>
                <td class="text-right" colspan="{{ $colspan }}"><b>@lang('total')</b></td>
                <td class="text-right"><b>{{ format_currency($invoice->invoice_total) }}</b></td>
            </tr>
            <tr>
                <td class="text-right" colspan="{{ $colspan }}">@lang('paid')</td>
                <td class="text-right">{{ format_currency($invoice->invoice_paid) }}</td>
            </tr>
            <tr>
                <td class="text-right" colspan="{{ $colspan }}"><b>@lang('balance')</b></td>
                <td class="text-right{{ $text_class }}"><b>{{ format_currency($invoice->invoice_balance) }}</b></td>
            </tr>
            </tbody>
        </table>

        @if($show_qrcode)
            <table class="invoice-qr-code-table">
                <tr>
                    <td>
                        <div>
                            <strong>{{ trans('qr_code_settings_recipient') }}:</strong>
                            {{ $invoice->user_company ?: get_setting('qr_code_recipient') }}
                        </div>
                        <div>
                            <strong>{{ trans('qr_code_settings_iban') }}:</strong>
                            {{ $invoice->user_iban ?: get_setting('qr_code_iban') }}
                        </div>
                    </td>
                </tr>
            </table>
        @endif
</main>

<div class="invoice-terms">
    @if($invoice->invoice_terms)
        <div class="notes">
            <b>@lang('terms')</b><br />
            {!! nl2br(e($invoice->invoice_terms)) !!}
        </div>
    @endif
</div>

<htmlpagefooter name="footer">
    <footer>
        @lang('invoice') {{ $invoice->invoice_number }} - @lang('page') {PAGENO} / {nbpg}
    </footer>
</htmlpagefooter>
</body>
</html>
