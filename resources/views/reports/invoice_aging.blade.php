<!DOCTYPE html>
<html lang="@lang('cldr')">
<head>
    <title>{{ get_setting('custom_title', 'InvoicePlane', true) }} - @lang('invoice_aging')</title>
    <link rel="stylesheet" href="@php _theme_asset('css/reports.css'); " type="text/css">
</head>
<body>

<h3 class="report_title">@lang('invoice_aging')</h3>

<table>
    <tr>
        <th>@lang('client')</th>
        <th class="amount">@lang('invoice_aging_1_15')</th>
        <th class="amount">@lang('invoice_aging_16_30')</th>
        <th class="amount">@lang('invoice_aging_above_30')</th>
        <th class="amount">@lang('total')</th>
    </tr>
    @foreach($results as $result)
    <tr>
        <td>{!! format_client($result) !!}</td>
        <td class="amount">{{ format_currency($result->range_1) }}</td>
        <td class="amount">{{ format_currency($result->range_2) }}</td>
        <td class="amount">{{ format_currency($result->range_3) }}</td>
        <td class="amount">{{ format_currency($result->total_balance) }}</td>
    </tr>
    @php }
</table>

</body>
</html>
