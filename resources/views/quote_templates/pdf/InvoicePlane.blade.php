@php // Fix item table head when numerous (>= 12) items (overflowing in 2nd page)
$add_table_and_head_for_sums = 1; // Set to 0/false/null/'', return to original IP
// edit if you know what you're doing
$colspan = $show_item_discounts ? 5 : 4; @endphp<!DOCTYPE html>
<html lang="@lang('cldr'); @endphp">
<head>
    <meta charset="utf-8">
    <title>{{ get_setting('custom_title', 'InvoicePlane', true) }} - @lang('quote'); @endphp</title>
    <link rel="stylesheet" href="@php _theme_asset('css/templates.css'); @endphp" type="text/css">
    <link rel="stylesheet" href="@php _core_asset('css/custom-pdf.css'); @endphp" type="text/css">
</head>
<body>
<header class="clearfix">

    <div id="logo">
        {{ invoice_logo_pdf() }}
    </div>

    <div id="client">
        <div>
            <b>{!! format_client($quote) !!}</b>
        </div>
        @if($quote->client_vat_id) {
    echo '<div>' . trans('vat_id_short') . ': ' . htmlsc($quote->client_vat_id) . '</div>';
}
if ($quote->client_tax_code) {
    echo '<div>' . trans('tax_code_short') . ': ' . htmlsc($quote->client_tax_code) . '</div>';
}
if ($quote->client_address_1) {
    echo '<div>' . htmlsc($quote->client_address_1) . '</div>';
}
if ($quote->client_address_2) {
    echo '<div>' . htmlsc($quote->client_address_2) . '</div>';
}
if ($quote->client_city || $quote->client_state || $quote->client_zip) {
    echo '<div>';
    if ($quote->client_city) {
        echo htmlsc($quote->client_city) . ' InvoicePlane.php';
    }
    if ($quote->client_state) {
        echo htmlsc($quote->client_state) . ' InvoicePlane.php';
    }
    if ($quote->client_zip) {
        echo htmlsc($quote->client_zip);
    }
    echo '</div>';
}
if ($quote->client_country) {
    echo '<div>' . get_country_name(trans('cldr'), htmlsc($quote->client_country)) . '</div>';
}

echo '<br>';

if ($quote->client_phone) {
    echo '<div>' . trans('phone_abbr') . ': ' . htmlsc($quote->client_phone) . '</div>';
} @endphp

    </div>
    <div id="company">
        <div><b>{!! $quote->user_name !!}</b></div>
        @if($quote->user_vat_id) {
    echo '<div>' . trans('vat_id_short') . ': ' . htmlsc($quote->user_vat_id) . '</div>';
}
if ($quote->user_tax_code) {
    echo '<div>' . trans('tax_code_short') . ': ' . htmlsc($quote->user_tax_code) . '</div>';
}
if ($quote->user_address_1) {
    echo '<div>' . htmlsc($quote->user_address_1) . '</div>';
}
if ($quote->user_address_2) {
    echo '<div>' . htmlsc($quote->user_address_2) . '</div>';
}
if ($quote->user_city || $quote->user_state || $quote->user_zip) {
    echo '<div>';
    if ($quote->user_city) {
        echo htmlsc($quote->user_city) . ' InvoicePlane.php';
    }
    if ($quote->user_state) {
        echo htmlsc($quote->user_state) . ' InvoicePlane.php';
    }
    if ($quote->user_zip) {
        echo htmlsc($quote->user_zip);
    }
    echo '</div>';
}
if ($quote->user_country) {
    echo '<div>' . get_country_name(trans('cldr'), htmlsc($quote->user_country)) . '</div>';
}

echo '<br/>';

if ($quote->user_phone) {
    echo '<div>' . trans('phone_abbr') . ': ' . htmlsc($quote->user_phone) . '</div>';
}
if ($quote->user_fax) {
    echo '<div>' . trans('fax_abbr') . ': ' . htmlsc($quote->user_fax) . '</div>';
} @endphp
    </div>

</header>

<main>

    <div class="invoice-details clearfix">
        <table>
            <tr>
                <td>@lang('quote_date'); @endphp:</td>
                <td>{{ date_from_mysql($quote->quote_date_created, true) }}</td>
            </tr>
            <tr>
                <td>@lang('expires'); @endphp:</td>
                <td>{{ date_from_mysql($quote->quote_date_expires, true) }}</td>
            </tr>
            <tr>
                <td>@lang('total'); @endphp:</td>
                <td>{{ format_currency($quote->quote_total) }}</td>
            </tr>
        </table>
    </div>

    <h1 class="invoice-title">@lang('quote'); @endphp {!! $quote->quote_number !!}</h1>

    <table class="item-table">
        <thead>
        <tr>
            <th class="item-name">@lang('item'); @endphp</th>
            <th class="item-desc">@lang('description'); @endphp</th>
            <th class="item-amount text-right">@lang('qty'); @endphp</th>
            <th class="item-price text-right">@lang('price'); @endphp</th>
            @if($show_item_discounts) { @endphp
            <th class="item-discount text-right">@lang('discount'); @endphp</th>
            @php } @endphp
            <th class="item-total text-right">@lang('total'); @endphp</th>
        </tr>
        </thead>
        <tbody>

        @foreach($items as $item) { @endphp
        <tr>
            <td>{!! $item->item_name !!}</td>
            <td>{{ nl2br(htmlsc($item->item_description)) }}</td>
            <td class="text-right">
                {{ format_quantity($item->item_quantity) }}
                @if($item->item_product_unit) { @endphp
                <br>
                <small>{!! $item->item_product_unit !!}</small>
                @php } @endphp
            </td>
            <td class="text-right">
                {{ format_currency($item->item_price) }}
            </td>
            @if($show_item_discounts) { @endphp
            <td class="text-right">
                {{ format_currency($item->item_discount) }}
            </td>
            @php } @endphp
            <td class="text-right">
                {{ format_currency($item->item_total) }}
            </td>
        </tr>
        @php } @endphp

        </tbody>
        @php // Fix for mpdf: table head of items printed on 2nd page
if ($add_table_and_head_for_sums) {
    $colspan .= '" style="width:543px'; // little hackish @endphp
    </table>

    <table class="item-table">
        <thead>
        <tr>
            <th colspan="{{ $colspan ?>">&nbsp;</th>
            <th class="text-right">
                @lang('total') }}
            </th>
        </tr>
        </thead>
@php } // fi add_table_head_for_totals @endphp
        <tbody class="invoice-sums">

@if( ! $legacy_calculation) {
    discount_global_print_in_pdf($quote, $show_item_discounts, 'quote'); // in Helpers/pdf_helper
} @endphp

        <tr>
            <td class=" text-right
            " colspan="{{ $colspan @endphp">
                @lang('subtotal') }}
            </td>
            <td class="text-right">{{ format_currency($quote->quote_item_subtotal) }}</td>
        </tr>

        @php if ($quote->quote_item_tax_total > 0) { @endphp
        <tr>
            <td class="text-right" colspan="{{ $colspan @endphp">
                @lang('item_tax') }}
            </td>
            <td class=" text-right
            ">
            {{ format_currency($quote->quote_item_tax_total) }}
            </td>
        </tr>
        @php } @endphp

        @foreach($quote_tax_rates as $quote_tax_rate) { @endphp
        <tr>
            <td class="text-right" colspan="{{ $colspan @endphp">
                {{ $quote_tax_rate->quote_tax_rate_name . ' (' . format_amount($quote_tax_rate->quote_tax_rate_percent) . '%)' }}
            </td>
            <td class=" text-right
            ">
            {{ format_currency($quote_tax_rate->quote_tax_rate_amount) }}
            </td>
        </tr>
        @php } @endphp

        @if($legacy_calculation) {
    discount_global_print_in_pdf($quote, $show_item_discounts, 'quote'); // in Helpers/pdf_helper
} @endphp

        <tr>
            <td class="text-right" colspan="{{ $colspan @endphp">
                <b>@lang('total') }}</b>
            </td>
            <td class=" text-right
            ">
            <b>{{ format_currency($quote->quote_total) }}</b>
            </td>
        </tr>
        </tbody>
    </table>
</main>

<div class="invoice-terms">
    <?php
if ($quote->notes) { @endphp
    <div class="notes">
        <b>@lang('notes') }}</b><br/>
        {{ nl2br(htmlsc($quote->notes)) }}
    </div>
    @php } @endphp
</div>

<htmlpagefooter name="footer">
    <footer>
        @lang('quote'); @endphp {{ $quote->quote_number }} - @lang('page'); @endphp {PAGENO} / {nbpg}
    </footer>
</htmlpagefooter>

</body>
</html>
