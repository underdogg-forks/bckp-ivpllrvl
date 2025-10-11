<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 no-margin">

        <thead>
        <tr>
            <th>@lang('quote')</th>
            <th>@lang('created')</th>
            <th>@lang('due_date')</th>
            <th>@lang('client_name')</th>
            <th>@lang('amount')</th>
            <th>@lang('options')</th>
        </tr>
        </thead>

        <tbody>
        @foreach($quotes as $quote)
            <tr>
                <td>
                    <a href="{{ url('guest/quotes/view/' . $quote->quote_id) }}"
                       title="@lang('edit')">
                        {{ $quote->quote_number }}
                    </a>
                    @if($quote->quote_status_id == 4)
                        <span class="text-success">@lang('approved')</span>
                    @elseif($quote->quote_status_id == 5)
                        <span class="text-danger">@lang('rejected')</span>
                    @endif
                </td>
                <td>{{ date_from_mysql($quote->quote_date_created) }}</td>
                <td>{{ date_from_mysql($quote->quote_date_expires) }}</td>
                <td>{!! $quote->client_name !!}</td>
                <td>{{ format_currency($quote->quote_total) }}</td>
                <td>
                    <div class="options inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm">
                        <a class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" href="{{ url('guest/quotes/view/' . $quote->quote_id) " }}>
                            <i class="fa fa-eye"></i> @lang('view')
                        </a>
                        <a class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" target="_blank"
                           href="{{ url('guest/quotes/generate_pdf/' . $quote->quote_id) " }}>
                            <i class="fa fa-print"></i> @lang('pdf')
                        </a>
                        @if(in_array($quote->quote_status_id, [2, 3]))
                            <a class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors" href="{{ url('guest/quotes/approve/' . $quote->quote_id) " }}>
                                <i class="fa fa-check"></i> {{ trans('approve') }}
                            </a>
                            <a class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" href="{{ url('guest/quotes/reject/' . $quote->quote_id) " }}>
                                <i class="fa fa-ban"></i> {{ trans('reject') }}
                            </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
