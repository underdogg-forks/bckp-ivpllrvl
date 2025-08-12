<!DOCTYPE html>
<html lang="@lang('cldr'); @endphp">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title>
        {{ get_setting('custom_title', 'InvoicePlane', true) }}
        - @lang('quote'); @endphp {{ $quote->quote_number }}
    </title>

    <link rel="icon" href="@php _core_asset('img/favicon.png'); @endphp" type="image/png">
    <link rel="stylesheet" href="@php _theme_asset('css/style.css'); @endphp" type="text/css">
    <link rel="stylesheet" href="@php _core_asset('css/custom.css'); @endphp" type="text/css">
</head>
<body>

<div class="container">

    <div id="content">

        <div class="webpreview-header">

            <h2>@lang('quote'); @endphp&nbsp;{{ $quote->quote_number }}</h2>

            <div class="btn-group">
                @if(isset($_SESSION['user_id'], $_SESSION['user_type'])) { ?>
                <a href="{{ url($_SESSION['user_type'] > 1 ? 'guest' : '') }}"
                   class="btn btn-default" title="@lang('dashboard'); @endphp">
                    <i class="fa fa-dashboard"></i> @lang('dashboard'); @endphp
                </a>
                @php } @endphp
                @php if (in_array($quote->quote_status_id, [2, 3])) { @endphp
                <a href="{{ url('guest/view/approve_quote/' . $quote_url_key) }}"
                   class="btn btn-success">
                    <i class="fa fa-check"></i>@lang('approve_this_quote'); @endphp
                </a>
                <a href="{{ url('guest/view/reject_quote/' . $quote_url_key) }}"
                   class="btn btn-danger">
                    <i class="fa fa-times-circle"></i>@lang('reject_this_quote'); @endphp
                </a>
                @php } @endphp
                <a href="{{ url('guest/view/generate_quote_pdf/' . $quote_url_key) }}"
                   class="btn btn-primary">
                    <i class="fa fa-print"></i> @lang('download_pdf'); @endphp
                </a>
            </div>

        </div>

        <hr>

        @php if ($flash_message) { @endphp
        <div class="alert alert-info">
            {{ $flash_message }}
        </div>
        @php } else {
    echo '<br>';
} @endphp

        <div class="quote">

            @php if ($logo = invoice_logo()) {
    echo $logo . '<br><br>';
} @endphp

            <div class="row">
                <div class="col-xs-12 col-md-6 col-lg-5">

                    <h4>@php _htmlsc(format_client($quote)); @endphp</h4>
                    <p>@php if ($quote->user_vat_id) {
                                echo @lang('vat_id_short') . ': ' . $quote->user_vat_id . '<br>';
                            }
if ($quote->user_tax_code) {
    echo @lang('tax_code_short') . ': ' . $quote->user_tax_code . '<br>';
}
if ($quote->user_address_1) {
    echo htmlsc($quote->user_address_1) . '<br>';
}
if ($quote->user_address_2) {
    echo htmlsc($quote->user_address_2) . '<br>';
}
if ($quote->user_city) {
    echo htmlsc($quote->user_city) . ' InvoicePlane_Web.php';
}
if ($quote->user_state) {
    echo htmlsc($quote->user_state) . ' InvoicePlane_Web.php';
}
if ($quote->user_zip) {
    echo htmlsc($quote->user_zip) . '<br>';
}
if ($quote->user_phone) {
    @lang('phone_abbr');
    echo ': ' . htmlsc($quote->user_phone) . '<br>';
}
if ($quote->user_fax) {
    @lang('fax_abbr');
    echo ': ' . htmlsc($quote->user_fax);
} @endphp</p>

                </div>
                <div class="col-lg-2"></div>
                <div class="col-xs-12 col-md-6 col-lg-5 text-right">

                    <h4>@php _htmlsc($quote->client_name); @endphp</h4>
                    <p>@php if ($quote->client_vat_id) {
    @lang('vat_id_short');
    echo ': ' . $quote->client_vat_id . '<br>';
}
if ($quote->client_tax_code) {
    @lang('tax_code_short');
    echo ': ' . $quote->client_tax_code . '<br>';
}
if ($quote->client_address_1) {
    echo htmlsc($quote->client_address_1) . '<br>';
}
if ($quote->client_address_2) {
    echo htmlsc($quote->client_address_2) . '<br>';
}
if ($quote->client_city) {
    echo htmlsc($quote->client_city) . ' InvoicePlane_Web.php';
}
if ($quote->client_state) {
    echo htmlsc($quote->client_state) . ' InvoicePlane_Web.php';
}
if ($quote->client_zip) {
    echo htmlsc($quote->client_zip) . '<br>';
}
if ($quote->client_phone) {
    @lang('phone_abbr');
    echo ': ' . htmlsc($quote->client_phone) . '<br>';
} @endphp</p>

                    <br>

                    <table class="table table-condensed">
                        <tbody>
                        <tr>
                            <td>@lang('quote_date'); @endphp</td>
                            <td style="text-align:right;">{{ date_from_mysql($quote->quote_date_created) }}</td>
                        </tr>
                        <tr class="{{ $is_expired ? 'overdue' : '' ?>">
                                    <td>@lang('expires') }}</td>
                                    <td class=" amount
                        ">
                        {{ date_from_mysql($quote->quote_date_expires) }}
                        </td>
                        </tr>
                        <tr>
                            <td><?php @lang('total'); @endphp</td>
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
                            <th>@lang('item'); @endphp</th>
                            <th>@lang('description'); @endphp</th>
                            <th class="amount">@lang('qty'); @endphp</th>
                            <th class="amount">@lang('price'); @endphp</th>
                            <th class="amount">@lang('discount'); @endphp</th>
                            <th class="amount">@lang('total'); @endphp</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php foreach ($items as $item) { @endphp
                        <tr>
                            <td>@php _htmlsc($item->item_name); @endphp</td>
                            <td>{{ nl2br(htmlsc($item->item_description)) }}</td>
                            <td class="amount">
                                {{ format_quantity($item->item_quantity) }}
                                @php if ($item->item_product_unit)
                                            <br>
                                            <small><?php _htmlsc($item->item_product_unit); @endphp</small>
                                @endif
                            </td>
                            <td class="amount">{{ format_currency($item->item_price) }}</td>
                            <td class="amount">{{ format_currency($item->item_discount) }}</td>
                            <td class="amount">{{ format_currency($item->item_subtotal) }}</td>
                        </tr>
                        @php } @endphp

                        @php if ( ! $legacy_calculation) { @endphp
                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount">@lang('discount'); @endphp</td>
                            <td class="amount">@php if ($quote->quote_discount_percent > 0) {
                                                echo format_amount($quote->quote_discount_percent) . '&nbsp;%';
                                            } else {
                                                echo format_currency($quote->quote_discount_amount);
                                            } @endphp</td>
                        </tr>
                        @php } @endphp

                        <tr>
                            <td colspan="4"></td>
                            <td class="amount">@lang('subtotal'); @endphp:</td>
                            <td class="amount">{{ format_currency($quote->quote_item_subtotal) }}</td>
                        </tr>

                        @php if ($quote->quote_item_tax_total > 0) { @endphp
                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount">@lang('item_tax'); @endphp</td>
                            <td class="amount">{{ format_currency($quote->quote_item_tax_total) }}</td>
                        </tr>
                        @php } @endphp

                        @php foreach ($quote_tax_rates as $quote_tax_rate) { @endphp
                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount">
                                {!! $quote_tax_rate->quote_tax_rate_name) . ' InvoicePlane_Web.php' . format_amount($quote_tax_rate->quote_tax_rate_percent) . '&nbsp;%' }}
                            </td>
                            <td class="amount">{{ format_currency($quote_tax_rate->quote_tax_rate_amount !!}</td>
                        </tr>
                        @php } @endphp

                        @php if ($legacy_calculation) { @endphp
                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount">@lang('discount'); @endphp</td>
                            <td class="amount">@php if ($quote->quote_discount_percent > 0) {
                                                echo format_amount($quote->quote_discount_percent) . '&nbsp;%';
                                            } else {
                                                echo format_currency($quote->quote_discount_amount);
                                            } @endphp</td>
                        </tr>
                        @php } @endphp

                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount">@lang('total'); @endphp</td>
                            <td class="amount">{{ format_currency($quote->quote_total) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row">

@php if ($quote->notes) { @endphp
                        <div class="col-xs-12 col-md-6">
                            <h4>@lang('notes') }}</h4>
                                <p>{{ nl2br(htmlsc($quote->notes)) }}</p>
                </div>
                <?php
} @endphp

                @php if (count($attachments) > 0) { @endphp
                <div class="col-xs-12 col-md-6">
                    <h4>@lang('attachments'); @endphp</h4>
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            @php foreach ($attachments as $attachment) { @endphp
                            <tr class="attachments">
                                <td>{{ $attachment['name'] }}</td>
                                <td>
                                    <a href="{{ url('guest/get/attachment/' . $attachment['fullname']) }}"
                                       class="btn btn-primary btn-sm">
                                        <i class="fa fa-download"></i> @lang('download') @endphp
                                    </a>
                                </td>
                            </tr>
                            @php } @endphp
                        </table>
                    </div>
                </div>
                @php } @endphp

            </div>

        </div><!-- .quote-items -->
    </div><!-- .quote -->
</div><!-- #content -->
</div>

</body>
</html>
