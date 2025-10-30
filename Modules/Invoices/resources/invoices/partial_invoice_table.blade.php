<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

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
@foreach($invoices as $invoice) {
    // Disable read-only if not applicable
    @if(config('disable_read_only') == true) {
        $invoice->is_read_only = 0;
    }
    // Convert the dropdown menu to a dropup if invoice is after the invoice split
    $dropup = $invoice_idx > $invoice_list_split;

        <tr>
            <td>
                    <span class="label {{ $invoice_statuses[$invoice->invoice_status_id]['class']" }}>
                        {{ $invoice_statuses[$invoice->invoice_status_id]['label'];
    @if($invoice->invoice_sign == '-1') {
        &nbsp;<i class="fa fa-credit-invoice" title="@php
        @lang('credit_invoice')"></i>@php
                            }
                            @if($invoice->is_read_only) {
                        &nbsp;<i class="fa fa-read-only" title="@lang('read_only')"></i>@php
                            }
                            @if($invoice->invoice_is_recurring) {
                        &nbsp;<i class="fa fa-refresh" title="@lang('recurring')"></i>@endif
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
                    <span class="{{ $invoice->is_overdue ? 'font-overdue' : ''" }}>
                        {{ date_from_mysql($invoice->invoice_date_due) }}
                    </span>
            </td>

            <td>
                <a href="{{ url('clients/view/' . $invoice->client_id) }}"
                   title="@lang('view_client')">
                    {!! format_client($invoice) !!}
                </a>
            </td>

            <td class="amount {{ $invoice->invoice_sign == '-1' ? 'text-danger' : ''" }}>
                {{ format_currency($invoice->invoice_total) }}
            </td>

            <td class="amount last">
                {{ format_currency($invoice->invoice_balance) }}
            </td>

            <td>
                <div class="options inline-flex rounded-md shadow-sm {{ $dropup ? ' dropup' : ''" }}>
                    <a class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5" data-toggle="dropdown" href="#">
                        <i class="fa fa-cog"></i> @lang('options')
                    </a>
                    <ul class="absolute z-10 mt-2 min-w-[160px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg overflow-hidden">
                        @if($invoice->is_read_only != 1)
                        <li>
                            <a href="{{ url('invoices/view/' . $invoice->invoice_id) " }}>
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
                            <a href="{{ url('mailer/invoice/' . $invoice->invoice_id) " }}>
                                <i class="fa fa-send fa-margin"></i> @lang('send_email')
                            </a>
                        </li>
                        <li>
                            <a href="#" class="invoice-add-payment"
                               data-invoice-id="{{ $invoice->invoice_id }}"
                               data-invoice-balance="{{ $invoice->invoice_balance }}"
                               data-invoice-payment-method="{{ $invoice->payment_method " }}>
                                <i class="fa fa-money fa-margin"></i>
                                @lang('enter_payment')
                            </a>
                        </li>
                        @if($invoice->invoice_status_id == 1 || config('enable_invoice_deletion') === true && $invoice->is_read_only != 1)
                        <li>
                            <form action="{{ url('invoices/delete/' . $invoice->invoice_id) }}"
                                  method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
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
        @endforeach
        </tbody>
    </table>
</div>
