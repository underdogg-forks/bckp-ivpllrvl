<!DOCTYPE html>
<html lang="@lang('cldr')">
<head>
    <title>{{ get_setting('custom_title', 'InvoicePlane', true) }} - @lang('invoices_per_client')</title>
    <link rel="stylesheet" href="@php _theme_asset('css/reports.css'); " type="text/css">
</head>
<body>

<h3 class="report_title">@lang('invoices_per_client')<br><small>{{ $from_date . ' - ' . $to_date ?></small></h3>

    <table>
@php $client_id = '';
foreach ($results as $result) {
    if ($client_id != $result->client_id) {
        $client_id = $result->client_id }}
        <tr>
            <th><?php htmlspecialchars(format_client($result)); </th>
            <th></th>
            <th></th>
        </tr>
        @php }
        <tr>
            <td>{{ date_from_mysql($result->invoice_date_created, true) }}</td>
            <td>{{ $result->invoice_number }}</td>
            <td class="amount">{{ format_currency($result->invoice_total) }}</td>
        </tr>
        @endforeach
        </table>

</body>
</html>
