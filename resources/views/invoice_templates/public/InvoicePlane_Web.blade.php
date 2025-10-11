<!DOCTYPE html>
<html lang="@lang('cldr')">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>
        {{ get_setting('custom_title', 'InvoicePlane', true) }}
        - @lang('invoice') {{ $invoice->invoice_number }}
    </title>

    <link rel="icon" href="{{ _core_asset('img/favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ _theme_asset('css/style.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ _core_asset('css/custom.css') }}" type="text/css">
</head>
<body>

<div class="container">
    <div id="content">

        <div class="webpreview-header">

            <h2>@lang('invoice')&nbsp;{{ $invoice->invoice_number }}</h2>

            <div class="btn-group">
                @if(isset($_SESSION['user_id'], $_SESSION['user_type'])) {
                ?>
                <a href="{{ url($_SESSION['user_type'] > 1 ? 'guest' : '') }}"
                   class="btn btn-default" title="@lang('dashboard')">
                    <i class="fa fa-dashboard"></i> @lang('dashboard')
                </a>
                @php }
                <a href="{{ url('guest/view/generate_' . ($invoice->sumex_id == null ? 'invoice' : 'sumex') . '_pdf/' . $invoice_url_key) }}"
                   class="btn btn-primary">
                    <i class="fa fa-print"></i> @lang('download_pdf')
                </a>
                @if(get_setting('enable_online_payments') == 1 && $invoice->invoice_balance > 0)

                <a href="{{ url('guest/payment_information/form/' . $invoice_url_key) }}"
                   class="btn btn-success">
                    <i class="fa fa-credit-card"></i> @lang('pay_now')
                </a>

@endif
            </div>

        </div>

        <hr>

        @include('layout.alerts')

        <div class="invoice">

            @php $logo = invoice_logo();
if ($logo)
{$logo  <br><br>}
@endif

            <div class="row">
                <div class="col-xs-12 col-md-6 col-lg-5">

                    <h4>{!! $invoice->user_name !!}</h4>
                    <p>@if($invoice->user_vat_id)
{trans(vat_id_short)  :   $invoice->user_vat_id  <br>}
@endif
if ($invoice->user_tax_code)
{trans(tax_code_short)  :   $invoice->user_tax_code  <br>}
@endif
if ($invoice->user_address_1)
{htmlsc($invoice->user_address_1)  <br>}
@endif
if ($invoice->user_address_2)
{htmlsc($invoice->user_address_2)  <br>}
@endif
if ($invoice->user_city)
{htmlsc($invoice->user_city)   InvoicePlane_Webphp}
@endif
if ($invoice->user_state)
{htmlsc($invoice->user_state)   InvoicePlane_Webphp}
@endif
if ($invoice->user_zip)
{htmlsc($invoice->user_zip)  <br>}
@endif
if ($invoice->user_phone) {
    @lang('phone_abbr');
    echo ': ' . htmlsc($invoice->user_phone) . '<br>';
}
if ($invoice->user_fax) {
    @lang('fax_abbr');
    echo ': ' . htmlsc($invoice->user_fax);
} </p>

                </div>
                <div class="col-lg-2"></div>
                <div class="col-xs-12 col-md-6 col-lg-5 text-right">

                    <h4>{!! format_client($invoice) !!}</h4>
                    <p>@if($invoice->client_vat_id) {
        @lang('vat_id_short');
        echo ': ' . $invoice->client_vat_id . '<br>';
    }
if ($invoice->client_tax_code) {
    @lang('tax_code_short');
    echo ': ' . $invoice->client_tax_code . '<br>';
}
if ($invoice->client_address_1)
{htmlsc($invoice->client_address_1)  <br>}
@endif
if ($invoice->client_address_2)
{htmlsc($invoice->client_address_2)  <br>}
@endif
if ($invoice->client_city)
{htmlsc($invoice->client_city)   InvoicePlane_Webphp}
@endif
if ($invoice->client_state)
{htmlsc($invoice->client_state)   InvoicePlane_Webphp}
@endif
if ($invoice->client_zip)
{htmlsc($invoice->client_zip)  <br>}
@endif
if ($invoice->client_phone)
{trans(phone_abbr)  :   htmlsc($invoice->client_phone)  <br>}
@endif </p>

                    <br>

                    <table class="table table-condensed">
                        <tbody>
                        <tr>
                            <td>@lang('invoice_date')</td>
                            <td style="text-align:right;">{{ date_from_mysql($invoice->invoice_date_created) }}</td>
                        </tr>
                        <tr class="{{ $is_overdue ? 'overdue' : '' ?>">
                                    <td>@lang('due_date') }}</td>
                                    <td class=" amount
                        ">
                        {{ date_from_mysql($invoice->invoice_date_due) }}
                        </td>
                        </tr>
                        <tr class="{{ $is_overdue ? 'overdue' : '' ">
                                    <td>@lang('amount_due') }}</td>
                                    <td style=" text-align:right;
                        ">{{ format_currency($invoice->invoice_balance) }}</td>
                        </tr>
                        @if($payment_method)

                        <tr>
                            <td>@lang('payment_method')</td>
                            <td>{!! $payment_method->payment_method_name !!}</td>
                        </tr>

@endif
                        </tbody>
                    </table>

                </div>
            </div>

            <br>

            <div class="invoice-items">
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
                        </tr>
                        @endforeach

                        @if( ! $legacy_calculation)

                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount">@lang('discount')</td>
                            <td class="amount">@if($invoice->invoice_discount_percent > 0) {
                                                {{ format_amount($invoice->invoice_discount_percent) . '&nbsp }}%';
                                            } else {
                                                {{ format_currency($invoice->invoice_discount_amount) }}
                                            } </td>
                        </tr>@endforeach

                        <tr>
                            <td colspan="4"></td>
                            <td class="amount">@lang('subtotal'):</td>
                            <td class="amount">{{ format_currency($invoice->invoice_item_subtotal) }}</td>
                        </tr>

                        @if($invoice->invoice_item_tax_total > 0)

                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount">@lang('item_tax')</td>
                            <td class="amount">{{ format_currency($invoice->invoice_item_tax_total) }}</td>
                        </tr>@endforeach

                        @foreach($invoice_tax_rates as $invoice_tax_rate)
                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount">
                                {!! $invoice_tax_rate->invoice_tax_rate_name) . ' InvoicePlane_Web.php' . format_amount($invoice_tax_rate->invoice_tax_rate_percent) . '&nbsp;%' }}
                            </td>
                            <td class="amount">{{ format_currency($invoice_tax_rate->invoice_tax_rate_amount !!}</td>
                        </tr>
                        @php }

                        @if($legacy_calculation)

                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount">@lang('discount')</td>
                            <td class="amount">@if($invoice->invoice_discount_percent > 0) {
                                                {{ format_amount($invoice->invoice_discount_percent) . '&nbsp }}%';
                                            } else {
                                                {{ format_currency($invoice->invoice_discount_amount) }}
                                            } </td>
                        </tr>@endforeach

                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount">@lang('total'):</td>
                            <td class="amount">{{ format_currency($invoice->invoice_total) }}</td>
                        </tr>

                        <tr>
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount">@lang('paid')</td>
                            <td class="amount">{{ format_currency($invoice->invoice_paid) </td>
                                </tr>
                                <tr class="{{ ($invoice->invoice_balance > 0) ? 'overdue' : 'text-success' }}">
                            <td class="no-bottom-border" colspan="4"></td>
                            <td class="amount">@php @lang('balance') }}</td>
                            <td class="amount">
                                <b>{{ format_currency($invoice->invoice_balance) </b>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

@if($invoice->invoice_balance == 0)
{<span class="stamp rotate bottom paid">  trans(paid)  </span>}@endforeach elseif ($is_overdue)
{<span class="stamp rotate bottom overdue">  trans(overdue)  </span>}@endforeach

                </div><!-- .invoice-items -->

                <hr>

@if(get_setting('qr_code') && $invoice->invoice_balance > 0)

                <table class="invoice-qr-code-table">
                    <tbody>
                        <tr>
                            <td>
                                <div>
                                    <strong>@lang('qr_code_settings_recipient') }}:</strong>
                    {{ $invoice->user_company ?: get_setting('qr_code_recipient') }}
                </div>
                <div>
                    <strong><?php @lang('qr_code_settings_iban'):</strong>
                    {{ $invoice->user_iban ?: get_setting('qr_code_iban') }}
                </div>
                <div>
                    <strong>@lang('qr_code_settings_bic'):</strong>
                    {{ $invoice->user_bic ?: get_setting('qr_code_bic') }}
                </div>
                <div>
                    <strong>@lang('qr_code_settings_remittance_text'):</strong>
                    {{ parse_template($invoice, $invoice->user_remittance_text ?: get_setting('qr_code_remittance_text')) }}
                </div>
                </td>
                <td class="amount">
                    {{ invoice_qrcode($invoice->invoice_id) }}
                </td>
                </tr>
                </tbody>
                </table>

                <hr>@endforeach

                <div class="row">

                    @if($invoice->invoice_terms)

                    <div class="col-xs-12 col-md-6">
                        <h4>@lang('terms')</h4>
                        <p>{{ nl2br(e($invoice->invoice_terms)) }}</p>
                    </div>@endforeach

                    @if(count($attachments) > 0)

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

            </div><!-- .invoice -->
        </div><!-- #content -->
    </div>

</body>
</html>
