<!DOCTYPE html>
<html lang="@lang('cldr'); @endphp">
<head>
    <title>{{ get_setting('custom_title', 'InvoicePlane', true) }} - @lang('invoices_per_client'); @endphp</title>
    <link rel="stylesheet" href="@php _theme_asset('css/reports.css'); @endphp" type="text/css">
</head>
<body>

<h3 class="report_title">@lang('invoices_per_client'); @endphp<br><small>{{ $from_date . ' - ' . $to_date ?></small></h3>

    <table>
@php $client_id = '';
foreach ($results as $result) {
    if ($client_id != $result->client_id) {
        $client_id = $result->client_id }}
        <tr>
            <th><?php htmlspecialchars(format_client($result)); @endphp</th>
            <th></th>
            <th></th>
        </tr>
        @php } @endphp
        <tr>
            <td>{{ date_from_mysql($result->invoice_date_created, true) }}</td>
            <td>{{ $result->invoice_number }}</td>
            <td class="amount">{{ format_currency($result->invoice_total) }}</td>
        </tr>
        @php } // End foreach @endphp
        </table>

</body>
</html>
