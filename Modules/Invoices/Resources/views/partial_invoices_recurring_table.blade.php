
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

        <thead>
        <tr>
            <th>@lang('status')</th>
            <th>@lang('base_invoice')</th>
            <th>@lang('client')</th>
            <th>@lang('start_date')</th>
            <th>@lang('end_date')</th>
            <th>@lang('every')</th>
            <th>@lang('next_date')</th>
            <th>@lang('options')</th>
        </tr>
        </thead>

        <tbody>
        @foreach($recurring_invoices as $invoice)
        <tr>
            <td>
                            <span class="label label-{{ $invoice->recur_status != 'active' ? 'default' : 'success'" }}>
                                @php
                                    _trans($invoice->recur_status)
                            </span>
            </td>
            <td>
                <a href="{{ url('invoices/view/' . $invoice->invoice_id) " }}>
                    {{ $invoice->invoice_number }}
                </a>
            </td>
            <td>{{ anchor('clients/view/' . $invoice->client_id, htmlsc(format_client($invoice))) }}</td>
            <td>{{ date_from_mysql($invoice->recur_start_date) }}</td>
            <td>{{ date_from_mysql($invoice->recur_end_date) }}</td>
            <td>{{ _trans($recur_frequencies[$invoice->recur_frequency]) }}</td>
            <td>{{ date_from_mysql($invoice->recur_next_date) }}</td>
            <td>
                <div class="options inline-flex rounded-md shadow-sm">
                    <a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5" data-toggle="dropdown">
                        <i class="fa fa-cog"></i> @lang('options')
                    </a>
                    <ul class="absolute z-10 mt-2 min-w-[160px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg overflow-hidden">
                        <li>
                            <a href="{{ url('invoices/recurring/stop/' . $invoice->invoice_recurring_id) " }}>
                                <i class="fa fa-ban fa-margin"></i> @lang('stop')
                            </a>
                        </li>
                        <li>
                            <form action="{{ url('invoices/recurring/delete/' . $invoice->invoice_recurring_id) }}"
                                  method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                        onclick="return confirm('@lang('delete_invoice_warning')');">
                                    <i class="fa fa-trash-o fa-margin"></i> @lang('delete')
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>@endforeach
        </tbody>

    </table>
</div>
