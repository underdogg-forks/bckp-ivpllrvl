<!DOCTYPE html>
<html lang="@lang('cldr')">
<head>
    <title>{{ get_setting('custom_title', 'InvoicePlane', true) }} - @lang('payment_history')</title>
    <link rel="stylesheet" href="@php _theme_asset('css/reports.css'); " type="text/css">
</head>
<body>

<h3 class="report_title">@lang('payment_history')<br><small>{{ $from_date . ' - ' . $to_date ?></small></h3>

    <table>
        <tr>
            <th>@lang('date') }}</th>
        <th><?php @lang('invoice')</th>
        <th>@lang('client')</th>
        <th>@lang('payment_method')</th>
        <th>@lang('note')</th>
        <th class="amount">@lang('amount')</th>
        </tr>
        @php $sum = 0;
foreach ($results as $result) {
        <tr>
            <td>{{ date_from_mysql($result->payment_date, true) }}</td>
            <td>{{ $result->invoice_number }}</td>
            <td>{{ format_client($result) }}</td>
            <td>{!! $result->payment_method_name !!}</td>
            <td>{{ nl2br(e($result->payment_note)) }}</td>
            <td class="amount">{{ format_currency($result->payment_amount) }}</td>
        </tr>
        @php $sum += $result->payment_amount;
}

if ( ! empty($results)) {
        <tr>
            <td colspan=5>@lang('total')</td>
            <td class="amount">{{ format_currency($sum) }}</td>
        </tr>
        @php }
        </table>

</body>
</html>
