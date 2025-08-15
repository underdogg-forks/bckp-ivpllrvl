
<div class="table-responsive">
    <table class="table table-hover table-striped">

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
        @php $quote_idx = 1;
$quote_count = count($quotes);
$quote_list_split = $quote_count > 3 ? $quote_count / 2 : 9999;
foreach ($quotes as $quote) {
    // Convert the dropdown menu to a dropup if quote is after the invoice split
    $dropup = $quote_idx > $quote_list_split;
        @endphp
        <tr>
            <td>
                    <span class="label {{ $quote_statuses[$quote->quote_status_id]['class'] }}">
                        {{ $quote_statuses[$quote->quote_status_id]['label'] }}
                    </span>
            </td>
            <td>
                <a href="{{ url('quotes/view/' . $quote->quote_id) }}"
                   title="@lang('edit')">
                    {{ $quote->quote_number ? $quote->quote_number : $quote->quote_id }}
                </a>
            </td>
            <td>
                {{ date_from_mysql($quote->quote_date_created) }}
            </td>
            <td>
                {{ date_from_mysql($quote->quote_date_expires) }}
            </td>
            <td>
                <a href="{{ url('clients/view/' . $quote->client_id) }}"
                   title="@lang('view_client')">
                    {!! format_client($quote) !!}
                </a>
            </td>
            <td class="amount last">
                {{ format_currency($quote->quote_total) }}
            </td>
            <td>
                <div class="options btn-group{{ $dropup ? ' dropup' : '' }}">
                    <a class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown"
                       href="#">
                        <i class="fa fa-cog"></i> @lang('options')
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ url('quotes/view/' . $quote->quote_id) }}">
                                <i class="fa fa-edit fa-margin"></i> @lang('edit')
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('quotes/generate_pdf/' . $quote->quote_id) }}"
                               target="_blank">
                                <i class="fa fa-print fa-margin"></i> @lang('download_pdf')
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('mailer/quote/' . $quote->quote_id) }}">
                                <i class="fa fa-send fa-margin"></i> @lang('send_email')
                            </a>
                        </li>
                        <li>
                            <form action="{{ url('quotes/delete/' . $quote->quote_id) }}"
                                  method="POST">
                                @csrf
                                <button type="submit" class="dropdown-button"
                                        onclick="return confirm('@lang('delete_quote_warning')');">
                                    <i class="fa fa-trash-o fa-margin"></i> @lang('delete')
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    <?php
    $quote_idx++;
@endforeach
</tbody >

    </table >
</div >
<?php
