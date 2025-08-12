<!DOCTYPE html>
<html lang="@lang('cldr'); @endphp">
<head>
    <title>{{ get_setting('custom_title', 'InvoicePlane', true) }} - @lang('sales_by_client'); @endphp</title>
    <link rel="stylesheet" href="@php _theme_asset('css/reports.css'); @endphp" type="text/css">
</head>
<body>

<h3 class="report_title">@lang('sales_by_client'); @endphp<br><small>{{ $from_date . ' - ' . $to_date ?></small></h3>

    <table>
        <tr>
            <th>@lang('client') }}</th>
        <th class="amount"><?php @lang('invoice_count'); @endphp</th>
        <th class="amount">@lang('sales'); @endphp</th>
        <th class="amount">@lang('sales_with_tax'); @endphp</th>
        </tr>
        @php foreach ($results as $result) { @endphp
        <tr>
            <td>@php _htmlsc(format_client($result)); @endphp</td>
            <td class="amount">{{ $result->invoice_count }}</td>
            <td class="amount">{{ format_currency($result->sales) }}</td>
            <td class="amount">{{ format_currency($result->sales_with_tax) }}</td>
        </tr>
        @php } @endphp
        </table>

</body>
</html>
