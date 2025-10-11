<div class="table-responsive">
    <table class="table table-hover table-striped no-margin">

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
                    <div class="options btn-group btn-group-sm">
                        <a class="btn btn-default" href="{{ url('guest/quotes/view/' . $quote->quote_id) " }}>
                            <i class="fa fa-eye"></i> @lang('view')
                        </a>
                        <a class="btn btn-default" target="_blank"
                           href="{{ url('guest/quotes/generate_pdf/' . $quote->quote_id) " }}>
                            <i class="fa fa-print"></i> @lang('pdf')
                        </a>
                        @if(in_array($quote->quote_status_id, [2, 3]))
                            <a class="btn btn-success" href="{{ url('guest/quotes/approve/' . $quote->quote_id) " }}>
                                <i class="fa fa-check"></i> {{ trans('approve') }}
                            </a>
                            <a class="btn btn-danger" href="{{ url('guest/quotes/reject/' . $quote->quote_id) " }}>
                                <i class="fa fa-ban"></i> {{ trans('reject') }}
                            </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
