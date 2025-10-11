<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

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

        <tr>
            <td>
                <a href="{{ url('guest/invoices/view/' . $invoice->invoice_id) " }}>
                    {{ $invoice->invoice_number }}
                </a>
            </td>
            <td>{{ date_from_mysql($invoice->invoice_date_created) }}</td>
            <td class="{{ $css_class" }}>{{ date_from_mysql($invoice->invoice_date_due) }}</td>
            <td>{!! format_client($invoice) !!}</td>
            <td>{{ format_currency($invoice->invoice_total) }}</td>
            <td>{{ format_currency($invoice->invoice_balance) }}</td>
            <td>
                <div class="options inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm">
                    <a class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" href="{{ url('guest/invoices/view/' . $invoice->invoice_id) " }}>
                        <i class="fa fa-eye"></i> @lang('view')
                    </a>
                    <a class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" target="_blank"
                       href="{{ url('guest/invoices/generate_pdf/' . $invoice->invoice_id) " }}>
                        <i class="fa fa-print"></i> @lang('pdf')
                    </a>
                    @php
                        // fix 404 when balance = 0.00
                        @if($enable_online_payments && $invoice->invoice_balance > 0 && $invoice->invoice_status_id != 4)
                    <a class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                       href="{{ url('guest/payment_information/form/' . $invoice->invoice_url_key) " }}>
                        <i class="fa fa-credit-card"></i> @lang('pay_now')
                    </a>
                    @elseif($invoice->invoice_balance == 0)
                    <button class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors disabled">
                        <i class="fa fa-check"></i> {{ trans('paid') }}
                    </button>
                    @endif
                </div>
            </td>
        </tr>
@endforeach
</tbody>
            </table>
        </div >
