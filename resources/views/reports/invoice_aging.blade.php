<!DOCTYPE html>
<html lang="@lang('cldr'); @endphp">
<head>
    <title>{{ get_setting('custom_title', 'InvoicePlane', true) }} - @lang('invoice_aging'); @endphp</title>
    <link rel="stylesheet" href="@php _theme_asset('css/reports.css'); @endphp" type="text/css">
</head>
<body>

<h3 class="report_title">@lang('invoice_aging'); @endphp</h3>

<table>
    <tr>
        <th>@lang('client'); @endphp</th>
        <th class="amount">@lang('invoice_aging_1_15'); @endphp</th>
        <th class="amount">@lang('invoice_aging_16_30'); @endphp</th>
        <th class="amount">@lang('invoice_aging_above_30'); @endphp</th>
        <th class="amount">@lang('total'); @endphp</th>
    </tr>
    @php foreach ($results as $result) { @endphp
    <tr>
        <td>@php _htmlsc(format_client($result)); @endphp</td>
        <td class="amount">{{ format_currency($result->range_1) }}</td>
        <td class="amount">{{ format_currency($result->range_2) }}</td>
        <td class="amount">{{ format_currency($result->range_3) }}</td>
        <td class="amount">{{ format_currency($result->total_balance) }}</td>
    </tr>
    @php } @endphp
</table>

</body>
</html>
