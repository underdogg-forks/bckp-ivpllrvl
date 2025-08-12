@php namespace Modules\Payments\Views; @endphp
<div class="table-responsive">
    <table class="table table-hover table-striped">

        <thead>
        <tr>
            <th>@@lang('payment_date')</th>
            <th>@@lang('invoice_date')</th>
            <th>@@lang('invoice')</th>
            <th>@@lang('client')</th>
            <th class="amount last">@@lang('amount')</th>
            <th>@@lang('payment_method')</th>
            <th>@@lang('note')</th>
            <th>@@lang('options')</th>
        </tr>
        </thead>

        <tbody>
@php foreach ($payments as $payment) {
    @endphp
            <tr>
                <td>{{ date_from_mysql($payment->payment_date) }}</td>
                <td>{{ date_from_mysql($payment->invoice_date_created) }}</td>
                <td>{{ anchor('invoices/view/' . $payment->invoice_id, $payment->invoice_number) }}</td>
                <td>
                    <a href="{{ url('clients/view/' . $payment->client_id) }}"
                       title="@@lang('view_client')">
                        @php
    _htmlsc(format_client($payment));
    @endphp
                    </a>
                </td>
                <td class="amount last">{{ format_currency($payment->payment_amount) }}</td>
                <td>@php
    _htmlsc($payment->payment_method_name);
    @endphp</td>
                <td>@php
    _htmlsc($payment->payment_note);
    @endphp</td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> @@lang('options')
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ url('payments/form/' . $payment->payment_id) }}">
                                    <i class="fa fa-edit fa-margin"></i>
                                    @@lang('edit')
                                </a>
                            </li>
                            <li>
                                <form action="{{ url('payments/delete/' . $payment->payment_id) }}"
                                      method="POST">
                                    @php
    _csrf_field();
    @endphp
                                    <button type="submit" class="dropdown-button"
                                            onclick="return confirm('@@lang('delete_record_warning')');">
                                        <i class="fa fa-trash-o fa-margin"></i> @@lang('delete')
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
<?php
}
// End foreach @endphp
        </tbody>

    </table>
</div>
<?php 