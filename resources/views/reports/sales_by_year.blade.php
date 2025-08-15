<!DOCTYPE html>
<html lang="@lang('cldr'); @endphp">
<head>
    <title>{{ get_setting('custom_title', 'InvoicePlane', true) }} - @lang('sales_by_date'); @endphp</title>
    <link rel="stylesheet" href="@php _theme_asset('css/reports.css'); @endphp" type="text/css">
</head>

<body>
<h3 class="report_title">@lang('sales_by_date'); @endphp<br><small>{{ $from_date . ' - ' . $to_date ?></small></h3>

    <table>

        <tr>
            <th style="width:15%;text-align:center;border-bottom: none;"> @lang('vat_id') }} </th>
        <th style="width:50%;text-align:center;border-bottom: none;"> <?php @lang('name'); @endphp </th>
        <th style="width:15%;text-align:center;border-bottom: none;"> @lang('period'); @endphp </th>
        <th style="width:20%;text-align:center;border-bottom: none;"> @lang('quantity'); @endphp </th>
        </tr>

        <tr>
            <td colspan="4" style="border-bottom: none;">
                <hr>
            </td>
        </tr>

        @php $initial_year = 0;
$final_year   = 0;
$numYears     = 1;
$numRows      = 1;
$contRows     = 0;
$contYears    = 0;
$pattern      = '/^payment_*/i';

foreach ($results as $result) {
    if ($final_year == 0) {
        foreach ($result as $index => $value) {
            if ($initial_year == 0) {
                $initial_year = (int) (mb_substr($index, 11, 4));
            }

            $aux = (int) (mb_substr($index, 11, 4));

            if ($aux > $final_year) {
                $final_year = $aux;
            }
        }
    }

    if ($contYears == 0 && ($final_year - $initial_year) > 0) {
        $numYears  = $final_year - $initial_year + 1;
        $contYears = 1;
    }

    if ($contRows == 0) {
        $numRows += $numYears * 4;
        $contRows = 1;
    } @endphp

        <tr>
            <td style="border-bottom: none;text-align:center;">{{ $result->VAT_ID }}</td>
            <td style="border-bottom: none;text-align:center;" rowspan="{{ $numRows }}"
                valign="top">{!! $result->Name !!}</td>
            <td style="border-bottom: none;text-align:center;">@lang('annual'); @endphp</td>
            <td style="border-bottom: none;text-align:center;">{{ format_currency($result->total_payment) }}</td>
        </tr>

        @foreach($result as $index => $value) {
            $quarter = mb_substr($index, 8, 2);
            $year    = mb_substr($index, 11, 4);

            if (preg_match($pattern, $index)) { @endphp
        <tr>
            <td style="border-bottom: none;">&nbsp;</td>
            <td style="border-bottom: none;text-align:center;">@switch($quarter)
@switch($quarter)
@php switch ($quarter) {
                                @@case(('t1')):
                                    echo trans('Q1') . 'sales_by_year.php/' . $year;
                                    @break
                                @@case(('t2')):
                                    echo trans('Q2') . 'sales_by_year.php/' . $year;
                                    @break
                                @@case(('t3')):
                                    echo trans('Q3') . 'sales_by_year.php/' . $year;
                                    @break
                                @@case(('t4')):
                                    echo trans('Q4') . 'sales_by_year.php/' . $year;
                                    @break
                            } @endphp
@endswitch
@endswitch</td>
            <td style="border-bottom: none;text-align:center;">{{ ($value > 0) ? format_currency($value) : '' }}</td>
        </tr>
        @php } // End if
        } // End foreach result @endphp
        <tr>
            <td colspan="4" style="border-bottom: none;">
                <hr>
            </td>
        </tr>
        @php } // End foreach results @endphp

        </table>

</body>
</html>
