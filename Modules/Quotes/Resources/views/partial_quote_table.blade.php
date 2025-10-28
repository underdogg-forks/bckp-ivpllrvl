<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead>
        <tr>
            <th>@lang('status')</th>
            <th>@lang('quote')</th>
            <th>@lang('created')</th>
            <th>@lang('due_date')</th>
            <th>@lang('client_name')</th>
            <th class="amount last">@lang('amount')</th>
            <th>@lang('options')</th>
        </tr>
        </thead>

        <tbody>
        @php
            $quote_idx = 1;
            $quote_count = count($quotes);
            $quote_list_split = $quote_count > 3 ? $quote_count / 2 : 9999;
        @endphp

        @foreach($quotes as $quote)
            @php
                $dropup = $quote_idx > $quote_list_split;
            @endphp

            <tr>
                <td>
                    <span class="label {{ $quote_statuses[$quote->quote_status_id]['class'] }}">
                        {{ $quote_statuses[$quote->quote_status_id]['label'] }}
                    </span>
                </td>
                <td>
                    <a href="{{ url('quotes/view/' . $quote->quote_id) }}" title="@lang('edit')">
                        {{ $quote->quote_number ?? $quote->quote_id }}
                    </a>
                </td>
                <td>{{ date_from_mysql($quote->quote_date_created) }}</td>
                <td>{{ date_from_mysql($quote->quote_date_expires) }}</td>
                <td>
                    <a href="{{ url('clients/view/' . $quote->client_id) }}" title="@lang('view_client')">
                        {!! format_client($quote) !!}
                    </a>
                </td>
                <td class="amount last">{{ format_currency($quote->quote_total) }}</td>
                <td>
                    <div class="options inline-flex rounded-md shadow-sm {{ $dropup ? 'dropup' : '' }}">
                        <a class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                           data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> @lang('options')
                        </a>
                        <ul class="absolute z-10 mt-2 min-w-[160px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg overflow-hidden">
                            <li>
                                <a href="{{ url('quotes/view/' . $quote->quote_id) }}">
                                    <i class="fa fa-edit fa-margin"></i> @lang('edit')
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('quotes/generate_pdf/' . $quote->quote_id) }}" target="_blank">
                                    <i class="fa fa-print fa-margin"></i> @lang('download_pdf')
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('mailer/quote/' . $quote->quote_id) }}">
                                    <i class="fa fa-send fa-margin"></i> @lang('send_email')
                                </a>
                            </li>
                            <li>
                                <form action="{{ url('quotes/delete/' . $quote->quote_id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                            onclick="return confirm('@lang('delete_quote_warning')');">
                                        <i class="fa fa-trash-o fa-margin"></i> @lang('delete')
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>

            @php $quote_idx++; @endphp
        @endforeach
        </tbody>
    </table>
</div>
