@php namespace Modules\Guest\Views; @endphp
<div class="table-responsive">
    <table class="table table-hover table-striped no-margin">

        <thead>
        <tr>
            <th>@@lang('quote')</th>
            <th>@@lang('created')</th>
            <th>@@lang('due_date')</th>
            <th>@@lang('client_name')</th>
            <th>@@lang('amount')</th>
            <th>@@lang('options')</th>
        </tr>
        </thead>

        <tbody>
@php foreach ($quotes as $quote) {
    @endphp
            <tr>
                <td>
                    <a href="{{ url('guest/quotes/view/' . $quote->quote_id) }}"
                       title="@@lang('edit')">
                        {{ $quote->quote_number }}
                    </a>
@php
    if ($quote->quote_status_id == 4) {
        @endphp
                    <span class="text-success">@@lang('approved')</span>
@php
    } elseif ($quote->quote_status_id == 5) {
        @endphp
                    <span class="text-danger">@@lang('rejected')</span>
@php
    }
    @endphp
                </td>
                <td>{{ date_from_mysql($quote->quote_date_created) }}</td>
                <td>{{ date_from_mysql($quote->quote_date_expires) }}</td>
                <td>@php
    _htmlsc($quote->client_name);
    @endphp</td>
                <td>{{ format_currency($quote->quote_total) }}</td>
                <td>
                    <div class="options btn-group btn-group-sm">
                        <a class="btn btn-default" href="{{ url('guest/quotes/view/' . $quote->quote_id) }}">
                            <i class="fa fa-eye"></i> @@lang('view')
                        </a>
                        <a class="btn btn-default" target="_blank" href="{{ url('guest/quotes/generate_pdf/' . $quote->quote_id) }}">
                            <i class="fa fa-print"></i> @@lang('pdf')
                        </a>
@php
    if (in_array($quote->quote_status_id, [2, 3])) {
        @endphp
                        <a class="btn btn-success" href="{{ url('guest/quotes/approve/' . $quote->quote_id) }}">
                            <i class="fa fa-check"></i> @@lang('approve')
                        </a>
                        <a class="btn btn-danger" href="{{ url('guest/quotes/reject/' . $quote->quote_id) }}">
                            <i class="fa fa-ban"></i> @@lang('reject')
                        </a>
@php
    }
    @endphp
                    </div>
                </td>
            </tr>
<?php
}
// End foreach @endphp
        </tbody>

    </table>
</div><?php 
