@php namespace Modules\Invoices\Views; @endphp
<div class="table-responsive">
    <table class="table table-hover table-striped">

        <thead>
        <tr>
            <th>@lang('status')</th>
            <th>@lang('invoice')</th>
            <th>@lang('created')</th>
            <th>@lang('due_date')</th>
            <th>@lang('client_name')</th>
            <th class="amount">@lang('amount')</th>
            <th class="amount last">@lang('balance')</th>
            <th>@lang('options')</th>
        </tr>
        </thead>

        <tbody>
        @php $invoice_idx = 1;
$invoice_count = count($invoices);
$invoice_list_split = $invoice_count > 3 ? $invoice_count / 2 : 9999;
foreach ($invoices as $invoice) {
    // Disable read-only if not applicable
    if ($this->config->item('disable_read_only') == true) {
        $invoice->is_read_only = 0;
    }
    // Convert the dropdown menu to a dropup if invoice is after the invoice split
    $dropup = $invoice_idx > $invoice_list_split;
        @endphp
        <tr>
            <td>
                    <span class="label {{ $invoice_statuses[$invoice->invoice_status_id]['class'] }}">
                        {{ $invoice_statuses[$invoice->invoice_status_id]['label'];
    if ($invoice->invoice_sign == '-1') {
        @endphp&nbsp;<i class="fa fa-credit-invoice" title="@php
        @lang('credit_invoice') }}"></i>@php
                            }
                            if ($invoice->is_read_only) {
                        @endphp&nbsp;<i class="fa fa-read-only" title="@lang('read_only')"></i>@php
                            }
                            if ($invoice->invoice_is_recurring) {
                        @endphp&nbsp;<i class="fa fa-refresh" title="@lang('recurring')"></i>@endif
                    </span>
            </td>

            <td>
                <a href="{{ url('invoices/view/' . $invoice->invoice_id) }}"
                   title="@lang('edit')">
                    {{ $invoice->invoice_number ? $invoice->invoice_number : $invoice->invoice_id }}
                </a>
            </td>

            <td>
                {{ date_from_mysql($invoice->invoice_date_created) }}
            </td>

            <td>
                    <span class="{{ $invoice->is_overdue ? 'font-overdue' : '' }}">
                        {{ date_from_mysql($invoice->invoice_date_due) }}
                    </span>
            </td>

            <td>
                <a href="{{ url('clients/view/' . $invoice->client_id) }}"
                   title="@lang('view_client')">
                    {!! format_client($invoice) !!}
                </a>
            </td>

            <td class="amount {{ $invoice->invoice_sign == '-1' ? 'text-danger' : '' }}">
                {{ format_currency($invoice->invoice_total) }}
            </td>

            <td class="amount last">
                {{ format_currency($invoice->invoice_balance) }}
            </td>

            <td>
                <div class="options btn-group{{ $dropup ? ' dropup' : '' }}">
                    <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-cog"></i> @lang('options')
                    </a>
                    <ul class="dropdown-menu">
                        @if($invoice->is_read_only != 1)
                        <li>
                            <a href="{{ url('invoices/view/' . $invoice->invoice_id) }}">
                                <i class="fa fa-edit fa-margin"></i> @lang('edit')
                            </a>
                        </li>
                        @endif
                        <li>
                            <a href="{{ url('invoices/generate_pdf/' . $invoice->invoice_id) }}"
                               target="_blank">
                                <i class="fa fa-print fa-margin"></i> @lang('download_pdf')
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('mailer/invoice/' . $invoice->invoice_id) }}">
                                <i class="fa fa-send fa-margin"></i> @lang('send_email')
                            </a>
                        </li>
                        <li>
                            <a href="#" class="invoice-add-payment"
                               data-invoice-id="{{ $invoice->invoice_id }}"
                               data-invoice-balance="{{ $invoice->invoice_balance }}"
                               data-invoice-payment-method="{{ $invoice->payment_method }}">
                                <i class="fa fa-money fa-margin"></i>
                                @lang('enter_payment')
                            </a>
                        </li>
                        @if($invoice->invoice_status_id == 1 || $this->config->item('enable_invoice_deletion') === true && $invoice->is_read_only != 1)
                        <li>
                            <form action="{{ url('invoices/delete/' . $invoice->invoice_id) }}"
                                  method="POST">
                                @csrf
                                <button type="submit" class="dropdown-button"
                                        onclick="return confirm('@lang('delete_invoice_warning')');">
                                    <i class="fa fa-trash-o fa-margin"></i> @lang('delete')
                                </button>
                            </form>
                        </li>
                        @endif
                    </ul>
                </div>
            </td>
        </tr>
<?php
    $invoice_idx++;
}
// End foreach invoices @endphp
        </tbody>

    </table>
</div>
<?php
