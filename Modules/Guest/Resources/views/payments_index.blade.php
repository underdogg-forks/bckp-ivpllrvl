@php namespace Modules\Guest\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@lang('payments')</h1>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('guest/payments/index'), 'mdl_payments') }}
    </div>

</div>

<div id="content" class="table-content">

    @include('layout.alerts')

    <div id="filter_results">
        <div class="table-responsive">
            <table class="table table-hover table-striped">

                <thead>
                <tr>
                    <th>@lang('date')</th>
                    <th>@lang('invoice')</th>
                    <th>@lang('amount')</th>
                    <th>@lang('payment_method')</th>
                    <th>@lang('note')</th>
                </tr>
                </thead>

                <tbody>
                @foreach($payments as $payment)
                <tr>
                    <td>{{ date_from_mysql($payment->payment_date) }}</td>
                    <td>
                        <a href="{{ url('guest/invoices/view/' . $payment->invoice_id) }}">
                            {{ $payment->invoice_number }}
                        </a>
                    </td>
                    <td>{{ format_currency($payment->payment_amount) }}</td>
                    <td>{{ $payment->payment_method_name }}</td>
                    <td>{!! $payment->payment_note !!}</td>
                </tr>
                    @endif
                </tbody>

            </table>
        </div>
    </div>

</div>
<?php
