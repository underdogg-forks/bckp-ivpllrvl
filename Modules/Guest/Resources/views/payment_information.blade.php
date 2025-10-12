
    <!DOCTYPE html>

<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="@lang('cldr')"> <![endif]-->
<!--[if IE 7]>
<html class="no-js ie7 oldie" lang="@lang('cldr')"> <![endif]-->
<!--[if IE 8]>
<html class="no-js ie8 oldie" lang="@lang('cldr')"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="@lang('cldr')"> <!--<![endif]-->

<head>
    <title>{{ get_setting('custom_title', 'InvoicePlane', true) }}</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="NOINDEX,NOFOLLOW">
    <meta name="_csrf" content="{{ csrf_token() }}">
    <meta name="csrf_token_name" content="{{ config_item('csrf_token_name') " }}>
    <meta name="csrf_cookie_name" content="{{ config_item('csrf_cookie_name') " }}>
    <meta name="legacy_calculation" content="{{ (int) config_item('legacy_calculation') " }}>

    <link rel="icon" href="@php _core_asset('img/favicon.png')" type="image/png">

    <link rel="stylesheet" href="{{ _theme_asset('css/style.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ _core_asset('css/custom.css') }}" type="text/css">

    @if(get_setting('monospace_amounts') == 1)
    <link rel="stylesheet" href="{{ _theme_asset('css/monospace.css') }}" type="text/css">
    @endif

        <!--[if lt IE 9]>
    <script src="{{ _core_asset('js/legacy.min.js') " }}></script>
    <![endif]-->

    <script src="{{ _core_asset('js/dependencies.min.js') " }}></script>

</head>
<body>

<nav class="navbar navbar-default">
    <div class="container">

        <div class="navbar-brand">
            @lang('online_payment_for_invoice') #{{ $invoice->invoice_number }}
        </div>

        <ul class="nav navbar-nav navbar-right">
            <li>
                <a target="_blank" href="{{ url('guest/view/generate_invoice_pdf/' . $invoice->invoice_url_key) " }}>
                    <i class="fa fa-print"></i> @lang('download_pdf')
                </a>
            </li>
        </ul>

    </div>
</nav>

<div class="container">

    <div class="flex flex-wrap -mx-4">
        <div class="w-full px-4 col-md-8 col-md-offset-2">

            <br>
            @php $logo = invoice_logo();
@if($logo)
{$logo  <br><br>}
@endif

            <div class="mb-4">
                {{ $this->layout->loadView('layout/alerts', ['without_margin' => true]) }}
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">

                <div class="p-6">

                    <div class="flex flex-wrap -mx-4">
                        <div class="w-full px-4 col-md-7">
                            <h4>
                                {!! format_client($invoice) !!}
                            </h4>
                            <div class="client-address">
                                @include('clients/partial_client_address', ['client' => $invoice])
                            </div>
                        </div>

                        <div class="w-full px-4 col-md-5">
                            <div class="md:hidden lg:block lg:hidden"><br></div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700 table-condensed no-margin">
                                    <tbody>
                                    <tr>
                                        <td>{{ trans('invoice_date') }}</td>
                                        <td class="text-right">{{ date_from_mysql($invoice->invoice_date_created) }}</td>
                                    </tr>
                                    <tr class="{{ $is_overdue ? 'overdue' : ''" }}>
                                        <td>{{ trans('due_date') }}</td>
                                        <td class="text-right">
                                            {{ date_from_mysql($invoice->invoice_date_due) }}
                                        </td>
                                    </tr>
                                    <tr class="{{ $is_overdue ? 'overdue' : ''" }}>
                                        <td>{{ trans('total') }}</td>
                                        <td class="text-right">{{ format_currency($invoice->invoice_total) }}</td>
                                    </tr>
                                    <tr class="{{ $is_overdue ? 'overdue' : ''" }}>
                                        <td>{{ trans('balance') }}</td>
                                        <td class="text-right">{{ format_currency($invoice->invoice_balance) }}</td>
                                    </tr>
                                    @if($payment_method)
                                    <tr>
                                        <td>{{ trans('payment_method') . ': ' }}</td>
                                        <td class="text-right">{!! $payment_method->payment_method_name !!}</td>
                                    </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if(!empty($invoice->invoice_terms))
                        <div class="w-full px-4 text-muted">
                            <br>
                            <h4>@lang('terms')</h4>
                            <div>{!! nl2br($invoice->invoice_terms) !!}</div>
                        </div>
                        @endif
                    </div>

                </div>
            </div>
            @if($payment_provider == null && !$disable_form)
            <div>
                <p>{{ trans('select_payment_method') }}</p>
            </div>
            <ul class="list-group">
                @foreach($gateways as $gateway)
                <a class="list-group-item list-group-item-action"
                   href="{{ url('guest/payment_information/form/' . $invoice->invoice_url_key . '/' . $gateway) " }}>{{ ucwords(str_replace('_', ' ', $gateway)) }}</a>@endforeach
            </ul>@endforeach
        </div>
    </div>

</div>

<div id="modal-placeholder"></div>

{{ $this->layout->loadView('layout/includes/fullpage-loader') }}

<script defer src="@php _core_asset('js/scripts.min.js')"></script>
</body>
</html>
