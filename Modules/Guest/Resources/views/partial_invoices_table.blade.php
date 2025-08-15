
<div class="table-responsive">
    <table class="table table-hover table-striped">

        <thead>
        <tr>
            <th>@lang('invoice')</th>
            <th>@lang('created')</th>
            <th>@lang('due_date')</th>
            <th>@lang('client_name')</th>
            <th>@lang('amount')</th>
            <th>@lang('balance')</th>
            <th>@lang('options')</th>
        </tr>
        </thead>

        <tbody>
        @foreach($invoices as $invoice) {
    $css_class = $invoice->invoice_status_id != 4 && $invoice->invoice_date_due < date('Y-m-d') ? 'font-overdue' : '';
        @endphp
        <tr>
            <td>
                <a href="{{ url('guest/invoices/view/' . $invoice->invoice_id) }}">
                    {{ $invoice->invoice_number }}
                </a>
            </td>
            <td>{{ date_from_mysql($invoice->invoice_date_created) }}</td>
            <td class="{{ $css_class }}">{{ date_from_mysql($invoice->invoice_date_due) }}</td>
            <td>{!! format_client($invoice) !!}</td>
            <td>{{ format_currency($invoice->invoice_total) }}</td>
            <td>{{ format_currency($invoice->invoice_balance) }}</td>
            <td>
                <div class="options btn-group btn-group-sm">
                    <a class="btn btn-default" href="{{ url('guest/invoices/view/' . $invoice->invoice_id) }}">
                        <i class="fa fa-eye"></i> @lang('view')
                    </a>
                    <a class="btn btn-default" target="_blank"
                       href="{{ url('guest/invoices/generate_pdf/' . $invoice->invoice_id) }}">
                        <i class="fa fa-print"></i> @lang('pdf')
                    </a>
                    @php
                        // fix 404 when balance = 0.00
                        if ($enable_online_payments && $invoice->invoice_balance > 0 && $invoice->invoice_status_id != 4)
                    <a class="btn btn-primary"
                       href="{{ url('guest/payment_information/form/' . $invoice->invoice_url_key) }}">
                        <i class="fa fa-credit-card"></i> @lang('pay_now')
                    </a>
                    @elseif($invoice->invoice_balance == 0)
                    <button class="btn btn-success disabled">
                        <i class="fa fa-check"></i> @lang('paid')
                    </button>
                    @endif

                </div>
            </td>
        </tr>
    <?php
@endforeach
</tbody >

            </table >
        </div >
<?php
