
<div class="table-responsive">
    <table class="table table-hover table-striped">

        <thead>
        <tr>
            <th>@lang('id')</th>
            <th>@lang('invoice')</th>
            <th>@lang('transaction_successful')</th>
            <th>@lang('payment_date')</th>
            <th>@lang('payment_provider')</th>
            <th>@lang('provider_response')</th>
            <th>@lang('transaction_reference')</th>
        </tr>
        </thead>

        <tbody>
        @foreach($payment_logs as $log)
        <tr>
            <td>{{ $log->merchant_response_id }}</td>
            <td>
                <a href="{{ url('invoices/view/' . $log->invoice_id) }}"
                   title="@lang('invoice')">
                    {{ $log->invoice_number ? $log->invoice_number : $log->invoice_id }}
                </a>
            </td>
            <td>
                <i class="fa {{ $log->merchant_response_successful ? 'fa-check text-success' : 'fa-ban text-danger' " }}></i>
            </td>
            <td>{{ date_from_mysql($log->merchant_response_date) }}</td>
            <td>{{ $log->merchant_response_driver }}</td>
            <td class="small text-{{ $log->merchant_response_successful ? 'success' : 'danger' " }}>
                {{ $log->merchant_response }}
            </td>
            <td>{{ $log->merchant_response_reference }}</td>
        </tr>
            <?php
        @endforeach
        </tbody>

    </table>
</div>
