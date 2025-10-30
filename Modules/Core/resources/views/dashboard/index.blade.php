
<div id="content">
    {{ $this->layout->loadView('layout/alerts');
?>

    <div class="flex flex-wrap -mx-4 {{ get_setting('disable_quickactions') == 1 ? ' hidden' : ''" }}>
    <div class="w-full px-4">

        <div id="panel-quick-actions" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm quick-actions">

            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <b>@lang('quick_actions')</b>
            </div>

            <div class="-group inline-flex rounded-md shadow-sm -justified no-margin">
                <a href="{{ url('clients/form') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fa fa-user fa-margin"></i>
                    <span class="hidden sm:block">@lang('add_client')</span>
                </a>
                <a href="javascript:void(0)" class="create-quote inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fa fa-file fa-margin"></i>
                    <span class="hidden sm:block">@lang('create_quote')</span>
                </a>
                <a href="javascript:void(0)" class="create-invoice inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fa fa-file-text fa-margin"></i>
                    <span class="hidden sm:block">@lang('create_invoice')</span>
                </a>
                <a href="{{ url('payments/form') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fa fa-credit-card fa-margin"></i>
                    <span class="hidden sm:block">@lang('enter_payment')</span>
                </a>
            </div>

        </div>
    </div>
</div>

<div class="flex flex-wrap -mx-4">
    <div class="w-full px-4 md:w-1/2">

        <div id="panel-quote-overview" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overview">

            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <b><i class="fa fa-bar-chart fa-margin"></i> @lang('quote_overview')</b>
                <span class="float-right text-muted">{{ lang($quote_status_period) }}</span>
            </div>

            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-bordered table-condensed no-margin">
                @foreach($quote_status_totals as $total)
                    <tr>
                        <td>
                            <a href="{{ url($total['href']) " }}>
                                {{ $total['label'] }}
                            </a>
                        </td>
                        <td class="amount">
                            <span class="{{ $total['class']" }}>
                                {{ format_currency($total['sum_total']) }}
                            </span>
                        </td>
                    </tr>@endforeach
            </table>
        </div>

    </div>
    <div class="w-full px-4 md:w-1/2">

        <div id="panel-invoice-overview" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overview">

            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <b><i class="fa fa-bar-chart fa-margin"></i> @lang('invoice_overview')</b>
                <span class="float-right text-muted">{{ lang($invoice_status_period) }}</span>
            </div>

            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-bordered table-condensed no-margin">
                @foreach($invoice_status_totals as $total)
                    <tr>
                        <td>
                            <a href="{{ url($total['href']) " }}>
                                {{ $total['label'] }}
                            </a>
                        </td>
                        <td class="amount">
                            <span class="{{ $total['class']" }}>
                                {{ format_currency($total['sum_total']) }}
                            </span>
                        </td>
                    </tr>@endforeach
            </table>
        </div>
        @if(empty($overdue_invoices))
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm px-6 py-4 border-b bg-gray-50 dark:bg-gray-900">
                <span class="text-muted">@lang('no_overdue_invoices')</span>
            </div>
            @php
                } else {
                    $overdue_invoices_total = 0;
                    @foreach($overdue_invoices as $invoice) {
                        $overdue_invoices_total += $invoice->invoice_balance;
                    }

            <div class="bg-white dark:bg-gray-800 border border-red-200 dark:border-red-700 rounded-lg shadow-sm px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                {{ anchor('invoices/status/overdue', '<i class="fa fa-external-link"></i> ' . trans('overdue_invoices'), 'class="text-danger"') }}
                <span class="float-right text-danger">
                    {{ format_currency($overdue_invoices_total) }}
                </span>
            </div>@endforeach

    </div>
</div>

<div class="flex flex-wrap -mx-4">
    <div class="w-full px-4 md:w-1/2">

        <div id="panel-recent-quotes" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">

            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <b><i class="fa fa-history fa-margin"></i> @lang('recent_quotes')</b>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-condensed no-margin">
                    <thead>
                    <tr>
                        <th>@lang('status')</th>
                        <th style="min-width: 15%;">@lang('date')</th>
                        <th style="min-width: 15%;">@lang('quote')</th>
                        <th style="min-width: 35%;">@lang('client')</th>
                        <th class="amount">@lang('balance')</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($quotes as $quote)
                        <tr>
                            <td>
                                <span class="label {{ $quote_statuses[$quote->quote_status_id]['class']" }}>
                                    {{ $quote_statuses[$quote->quote_status_id]['label'] }}
                                </span>
                            </td>
                            <td>
                                {{ date_from_mysql($quote->quote_date_created) }}
                            </td>
                            <td>
                                {{ anchor('quotes/view/' . $quote->quote_id, $quote->quote_number ? $quote->quote_number : $quote->quote_id) }}
                            </td>
                            <td>
                                {{ anchor('clients/view/' . $quote->client_id, htmlsc(format_client($quote))) }}
                            </td>
                            <td class="amount">
                                {{ format_currency($quote->quote_total) }}
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ url('quotes/generate_pdf/' . $quote->quote_id) }}"
                                   target="_blank" title="@lang('download_pdf')">
                                    <i class="fa fa-file-pdf-o"></i>
                                </a>
                            </td>
                        </tr>@endforeach
                        <tr>
                            <td colspan="6" class="text-right small">
                                {{ anchor('quotes/status/all', trans('view_all')) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <div class="w-full px-4 md:w-1/2">

        <div id="panel-recent-invoices" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">

            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <b><i class="fa fa-history fa-margin"></i> @lang('recent_invoices')</b>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-condensed no-margin">
                    <thead>
                    <tr>
                        <th>@lang('status')</th>
                        <th style="min-width: 15%;">@lang('due_date')</th>
                        <th style="min-width: 15%;">@lang('invoice')</th>
                        <th style="min-width: 35%;">@lang('client')</th>
                        <th class="amount">@lang('balance')</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($invoices as $invoice) {
                    @if(config('disable_read_only') == true) {
                    $invoice->is_read_only = 0;
                    }

                    <tr>
                        <td>
                                    <span class="label {{ $invoice_statuses[$invoice->invoice_status_id]['class']" }}>
                                        {{ $invoice_statuses[$invoice->invoice_status_id]['label'];
    @if($invoice->invoice_sign == '-1')&nbsp;<i class="fa fa-credit-invoice" title="@php
        @lang('credit_invoice')"></i>@php
                                            }
                                            @if($invoice->is_read_only) {
                                        &nbsp;<i class="fa fa-read-only" title="@lang('read_only')"></i>@php
                                            }
                                            @if($invoice->invoice_is_recurring) {
                                        &nbsp;<i class="fa fa-refresh" title="@lang('recurring')"></i>@endforeach
                                    </span>
                        </td>
                        <td>
                                    <span class="{{ $invoice->is_overdue ? 'font-overdue' : ''" }}>
                                        {{ date_from_mysql($invoice->invoice_date_due) }}
                                    </span>
                        </td>
                        <td>
                            {{ anchor('invoices/view/' . $invoice->invoice_id, $invoice->invoice_number ? $invoice->invoice_number : $invoice->invoice_id) }}
                        </td>
                        <td>
                            {{ anchor('clients/view/' . $invoice->client_id, htmlsc(format_client($invoice))) }}
                        </td>
                        <td class="amount">
                            {{ format_currency($invoice->invoice_balance * $invoice->invoice_sign) }}
                        </td>
                        <td style="text-align: center;">
                            @if($invoice->sumex_id != null)
                                <a href="{{ url('invoices/generate_sumex_pdf/' . $invoice->invoice_id) }}"
                                   target="_blank" title="@lang('generate_sumex')">
                                    <i class="fa fa-file-pdf-o"></i>
                                </a>
                            @else
                                <a href="{{ url('invoices/generate_pdf/' . $invoice->invoice_id) }}"
                                   target="_blank" title="@lang('download_pdf')">
                                    <i class="fa fa-file-pdf-o"></i>
                                </a>@endforeach
                        </td>
                    </tr>@endforeach
                    <tr>
                        <td colspan="6" class="text-right small">
                            {{ anchor('invoices/status/all', trans('view_all')) }}
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>

    </div>
</div>

@if(get_setting('projects_enabled') == 1)
    <div class="flex flex-wrap -mx-4">
        <div class="w-full px-4 md:w-1/2">

            <div id="panel-projects" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">

                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <b><i class="fa fa-list fa-margin"></i> @lang('projects')</b>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-condensed no-margin">
                        <thead>
                        <tr>
                            <th>@lang('project_name')</th>
                            <th>@lang('client_name')</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($projects as $project)
                            <tr>
                                <td>
                                    {{ anchor('projects/view/' . $project->project_id, htmlsc($project->project_name)) }}
                                </td>
                                <td>
                                    @if($project->client_id != null)
                                        {{ anchor('clients/view/' . $project->client_id, htmlsc(format_client($project))) }}
                                    @else
                                        -@endforeach
                                </td>
                            </tr>@endforeach
                            <tr>
                                <td colspan="6" class="text-right small">
                                    {{ anchor('projects/index', trans('view_all')) }}
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
        <div class="w-full px-4 md:w-1/2">

            <div id="panel-recent-invoices" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">

                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <b><i class="fa fa-check-square-o fa-margin"></i> @lang('tasks')</b>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-condensed no-margin">

                        <thead>
                        <tr>
                            <th>@lang('status')</th>
                            <th>@lang('task_name')</th>
                            <th>@lang('task_finish_date')</th>
                            <th>@lang('project')</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($tasks as $task)
                            <tr>
                                <td>
                                    <span class="label {{ $task_statuses[$task->task_status]['class'] ?? ''" }}>
                                        @if(isset($task_statuses[$task->task_status]['label']))
{$task_statuses[$task->task_status][label]}@endforeach

                                    </span>
                                </td>
                                <td>
                                    {{ anchor('tasks/form/' . $task->task_id, htmlsc($task->task_name)) }}
                                </td>
                                <td>
                                    <span class="{{ $task->is_overdue ? 'font-overdue' : ''" }}>
                                        {{ date_from_mysql($task->task_finish_date) }}
                                    </span>
                                </td>
                                <td>
                                    {{ empty($task->project_id) ? '' : anchor('projects/view/' . $task->project_id, htmlsc($task->project_name)) }}
                                </td>
                            </tr>@endforeach
                            <tr>
                                <td colspan="6" class="text-right small">
                                    {{ anchor('tasks/index', trans('view_all')) }}
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>

            </div>

        </div>
    </div>

}
// End if projects_enabled

</div>
