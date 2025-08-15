<!DOCTYPE html>
<html lang="@lang('cldr'); @endphp">
<head>
    <title>{{ get_setting('custom_title', 'InvoicePlane', true) }} - @lang('payment_history'); @endphp</title>
    <link rel="stylesheet" href="@php _theme_asset('css/reports.css'); @endphp" type="text/css">
</head>
<body>

<h3 class="report_title">@lang('payment_history'); @endphp<br><small>{{ $from_date . ' - ' . $to_date ?></small></h3>

    <table>
        <tr>
            <th>@lang('date') }}</th>
        <th><?php @lang('invoice'); @endphp</th>
        <th>@lang('client'); @endphp</th>
        <th>@lang('payment_method'); @endphp</th>
        <th>@lang('note'); @endphp</th>
        <th class="amount">@lang('amount'); @endphp</th>
        </tr>
        @php $sum = 0;
foreach ($results as $result) { @endphp
        <tr>
            <td>{{ date_from_mysql($result->payment_date, true) }}</td>
            <td>{{ $result->invoice_number }}</td>
            <td>{{ format_client($result) }}</td>
            <td>{!! $result->payment_method_name !!}</td>
            <td>{{ nl2br(htmlsc($result->payment_note)) }}</td>
            <td class="amount">{{ format_currency($result->payment_amount) }}</td>
        </tr>
        @php $sum += $result->payment_amount;
}

if ( ! empty($results)) { @endphp
        <tr>
            <td colspan=5>@lang('total'); @endphp</td>
            <td class="amount">{{ format_currency($sum) }}</td>
        </tr>
        @php } @endphp
        </table>

</body>
</html>
