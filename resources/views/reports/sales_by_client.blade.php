<!DOCTYPE html>
<html lang="@lang('cldr')">
<head>
    <title>{{ get_setting('custom_title', 'InvoicePlane', true) }} - @lang('sales_by_client')</title>
    <link rel="stylesheet" href="@php _theme_asset('css/reports.css'); " type="text/css">
</head>
<body>

<h3 class="report_title">@lang('sales_by_client')<br><small>{{ $from_date . ' - ' . $to_date ?></small></h3>

    <table>
        <tr>
            <th>@lang('client') }}</th>
        <th class="amount"><?php @lang('invoice_count')</th>
        <th class="amount">@lang('sales')</th>
        <th class="amount">@lang('sales_with_tax')</th>
        </tr>
        @foreach($results as $result)
        <tr>
            <td>{!! format_client($result) !!}</td>
            <td class="amount">{{ $result->invoice_count }}</td>
            <td class="amount">{{ format_currency($result->sales) }}</td>
            <td class="amount">{{ format_currency($result->sales_with_tax) }}</td>
        </tr>
        @php }
        </table>

</body>
</html>
