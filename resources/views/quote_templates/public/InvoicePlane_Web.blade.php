<!DOCTYPE html>
<html lang="@lang('cldr')">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title>
        {{ get_setting('custom_title', 'InvoicePlane', true) }}
        - @lang('quote') {{ $quote->quote_number }}
    </title>

    <link rel="icon" href="@php _core_asset('img/favicon.png'); " type="image/png">
    <link rel="stylesheet" href="@php _theme_asset('css/style.css'); " type="text/css">
    <link rel="stylesheet" href="@php _core_asset('css/custom.css'); " type="text/css">
</head>
<body>

<div class="container">

    <div id="content">

        <div class="webpreview-header">

            <h2>@lang('quote')&nbsp;{{ $quote->quote_number }}</h2>

            <div class="btn-group">
                @if(isset($_SESSION['user_id'], $_SESSION['user_type'])) { ?>
                <a href="{{ url($_SESSION['user_type'] > 1 ? 'guest' : '') }}"
                   class="btn btn-default" title="@lang('dashboard')">
                    <i class="fa fa-dashboard"></i> @lang('dashboard')
                </a>
                @php }
                @if(in_array($quote->quote_status_id, [2, 3]))

                <a href="{{ url('guest/view/approve_quote/' . $quote_url_key) }}"
                   class="btn btn-success">
                    <i class="fa fa-check"></i>@lang('approve_this_quote')
                </a>
                <a href="{{ url('guest/view/reject_quote/' . $quote_url_key) }}"
                   class="btn btn-danger">
                    <i class="fa fa-times-circle"></i>@lang('reject_this_quote')
                </a>

@endif
                <a href="{{ url('guest/view/generate_quote_pdf/' . $quote_url_key) }}"
                   class="btn btn-primary">
                    <i class="fa fa-print"></i> @lang('download_pdf')
                </a>
            </div>

        </div>

        <hr>

        @if($flash_message)

        <div class="alert alert-info">
            {{ $flash_message }}
        </div>
        @php } else {
    {{ '<br>' }}
}

        <div class="quote">

            @if($logo = invoice_logo()) {
    {{ $logo . '<br><br>' }}
}

            <div class="row">
                <div class="col-xs-12 col-md-6 col-lg-5">

                    <h4>{!! format_client($quote) !!}</h4>
                    <p>@if($quote->user_vat_id) {
                                {{ @lang('vat_id_short') . ': ' . $quote->user_vat_id . '<br>' }}
                            }
if ($quote->user_tax_code) {
    {{ @lang('tax_code_short') . ': ' . $quote->user_tax_code . '<br>' }}
}
if ($quote->user_address_1) {
    {!! $quote->user_address_1) . '<br>' }}
}
if ($quote->user_address_2) {
    {!! $quote->user_address_2) . '<br>' }}
}
if ($quote->user_city) {
    {{ htmlsc($quote->user_city) . ' InvoicePlane_Web.php' }}
}
if ($quote->user_state) {
    {{ htmlsc($quote->user_state) . ' InvoicePlane_Web.php' }}
}
if ($quote->user_zip) {
    {{ htmlsc($quote->user_zip) . '<br>' }}
}
if ($quote->user_phone) {
    @lang('phone_abbr');
    {{ ': ' . htmlsc($quote->user_phone) . '<br>' }}
}
if ($quote->user_fax) {
    @lang('fax_abbr');
    {{ ': ' . htmlsc($quote->user_fax !!}
} </p>

                </div>
                <div class="col-lg-2"></div>
                <div class="col-xs-12 col-md-6 col-lg-5 text-right">

                    <h4>{!! $quote->client_name !!}</h4>
                    <p>@if($quote->client_vat_id) {
    @lang('vat_id_short');
    {{ ': ' . $quote->client_vat_id . '<br>' }}
}
if ($quote->client_tax_code) {
    @lang('tax_code_short');
    {{ ': ' . $quote->client_tax_code . '<br>' }}
}
if ($quote->client_address_1) {
    {!! $quote->client_address_1) . '<br>' }}
}
if ($quote->client_address_2) {
    {{ htmlsc($quote->client_address_2) . '<br>' }}
}
if ($quote->client_city) {
    {{ htmlsc($quote->client_city) . ' InvoicePlane_Web.php' }}
}
if ($quote->client_state) {
    {{ htmlsc($quote->client_state) . ' InvoicePlane_Web.php' }}
}
if ($quote->client_zip) {
    {{ htmlsc($quote->client_zip) . '<br>' }}
}
if ($quote->client_phone) {
    @lang('phone_abbr');
    {{ ': ' . htmlsc($quote->client_phone) . '<br>' }}
} </p>

                    <br>

                    <table class="table table-condensed">
                        <tbody>
                        <tr>
                            <td>@lang('quote_date')</td>
                            <td style="text-align:right;">{{ date_from_mysql($quote->quote_date_created !!}</td>
                        </tr>
                        <tr class="{{ $is_expired ? 'overdue' : '' ?>">
                                    <td>@lang('expires' !!}</td>
                                    <td class=" amount
                        ">
                        {{ date_from_mysql($quote->quote_date_expires) }}
                        </td>
                        </tr>
                        <tr>
                            <td>@php @lang('total')</td>
                            <td class="amount">{{ format_currency($quote->quote_total) }}</td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>

            <br>

            <div class="quote-items">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>@lang('item')</th>
                            <th>@lang('description')</th>
                            <th class="amount">@lang('qty')</th>
                            <th class="amount">@lang('price')</th>
                            <th class="amount">@lang('discount')</th>
                            <th class="amount">@lang('total')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td>{!! $item->item_name !!}</td>
                            <td>{{ nl2br(e($item->item_description)) }}</td>
                            <td class="amount">
                                {{ format_quantity($item->item_quantity) }}
                                @if($item->item_product_unit)
                                            <br>
                                            <small><?php htmlspecialchars($item->item_product_unit); </small>@endforeach
                            </td>
                            <td class="amount">{{ format_currency($item->item_price) }}</td>
                            <td class="amount">{{ format_currency($item->item_discount) }}</td>
                            <td class="amount">{{ format_currency($item->item_subtotal) }}</td>
                        </tr>@endforeach

                        @if( ! $legacy_calculation)

                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount">@lang('discount')</td>
                            <td class="amount">@if($quote->quote_discount_percent > 0) {
                                                {{ format_amount($quote->quote_discount_percent) . '&nbsp }}%';
                                            } else {
                                                {{ format_currency($quote->quote_discount_amount) }}
                                            } </td>
                        </tr>

@endif

                        <tr>
                            <td colspan="4"></td>
                            <td class="amount">@lang('subtotal'):</td>
                            <td class="amount">{{ format_currency($quote->quote_item_subtotal) }}</td>
                        </tr>

                        @if($quote->quote_item_tax_total > 0)

                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount">@lang('item_tax')</td>
                            <td class="amount">{{ format_currency($quote->quote_item_tax_total) }}</td>
                        </tr>

@endif

                        @foreach($quote_tax_rates as $quote_tax_rate)
                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount">
                                {!! $quote_tax_rate->quote_tax_rate_name) . ' InvoicePlane_Web.php' . format_amount($quote_tax_rate->quote_tax_rate_percent) . '&nbsp;%' }}
                            </td>
                            <td class="amount">{{ format_currency($quote_tax_rate->quote_tax_rate_amount !!}</td>
                        </tr>
                        @php }

                        @if($legacy_calculation)

                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount">@lang('discount')</td>
                            <td class="amount">@if($quote->quote_discount_percent > 0) {
                                                {{ format_amount($quote->quote_discount_percent) . '&nbsp }}%';
                                            } else {
                                                {{ format_currency($quote->quote_discount_amount) }}
                                            } </td>
                        </tr>@endforeach

                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount">@lang('total')</td>
                            <td class="amount">{{ format_currency($quote->quote_total) </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row">

@if($quote->notes)

                        <div class="col-xs-12 col-md-6">
                            <h4>@lang('notes') }}</h4>
                                <p>{{ nl2br(e($quote->notes)) }}</p>
                </div>@endforeach

                @if(count($attachments) > 0) {
                <div class="col-xs-12 col-md-6">
                    <h4>@lang('attachments')</h4>
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            @foreach($attachments as $attachment)
                            <tr class="attachments">
                                <td>{{ $attachment['name'] }}</td>
                                <td>
                                    <a href="{{ url('guest/get/attachment/' . $attachment['fullname']) }}"
                                       class="btn btn-primary btn-sm">
                                        <i class="fa fa-download"></i> @lang('download')
                                    </a>
                                </td>
                            </tr>@endforeach
                        </table>
                    </div>
                </div>
                @php }

            </div>

        </div><!-- .quote-items -->
    </div><!-- .quote -->
</div><!-- #content -->
</div>

</body>
</html>
