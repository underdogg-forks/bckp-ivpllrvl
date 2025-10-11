
<div class="table-responsive">
    <table class="table table-striped">

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
                            <span class="label label-{{ $invoice->recur_status != 'active' ? 'default' : 'success' " }}>
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
                <div class="options btn-group">
                    <a href="#" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-cog"></i> @lang('options')
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ url('invoices/recurring/stop/' . $invoice->invoice_recurring_id) " }}>
                                <i class="fa fa-ban fa-margin"></i> @lang('stop')
                            </a>
                        </li>
                        <li>
                            <form action="{{ url('invoices/recurring/delete/' . $invoice->invoice_recurring_id) }}"
                                  method="POST">
                                @csrf
                                <button type="submit" class="dropdown-button"
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
