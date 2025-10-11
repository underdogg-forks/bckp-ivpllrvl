<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

        <thead>
        <tr>
            <th>@lang('payment_date')</th>
            <th>@lang('invoice_date')</th>
            <th>@lang('invoice')</th>
            <th>@lang('client')</th>
            <th class="amount last">@lang('amount')</th>
            <th>@lang('payment_method')</th>
            <th>@lang('note')</th>
            <th>@lang('options')</th>
        </tr>
        </thead>

        <tbody>
        @foreach($payments as $payment)
            <tr>
                <td>{{ date_from_mysql($payment->payment_date) }}</td>
                <td>{{ date_from_mysql($payment->invoice_date_created) }}</td>
                <td>{!! anchor('invoices/view/' . $payment->invoice_id, $payment->invoice_number) !!}</td>
                <td>
                    <a href="{{ url('clients/view/' . $payment->client_id) }}"
                       title="@lang('view_client')">
                        {!! format_client($payment) !!}
                    </a>
                </td>
                <td class="amount last">{{ format_currency($payment->payment_amount) }}</td>
                <td>{!! $payment->payment_method_name !!}</td>
                <td>{!! $payment->payment_note !!}</td>
                <td>
                    <div class="options inline-flex rounded-md shadow-sm">
                        <a class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> @lang('options')
                        </a>
                        <ul class="absolute z-10 mt-2 min-w-[160px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg overflow-hidden">
                            <li>
                                <a href="{{ url('payments/form/' . $payment->payment_id) " }}>
                                    <i class="fa fa-edit fa-margin"></i>
                                    @lang('edit')
                                </a>
                            </li>
                            <li>
                                <form action="{{ url('payments/delete/' . $payment->payment_id) }}"
                                      method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                            onclick="return confirm('@lang('delete_record_warning')');">
                                        <i class="fa fa-trash-o fa-margin"></i> @lang('delete')
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
