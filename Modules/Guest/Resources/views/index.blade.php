
<div id="headerbar">
    <h1 class="headerbar-title">@lang('dashboard')</h1>
</div>

<div id="content">

    {{ $this->layout->loadView('layout/alerts') }}

    <div class="panel panel-default">

        <div class="panel-heading">@lang('quotes_requiring_approval')</div>

        <div class="panel-body no-padding">

            @if($open_quotes)
{$this->layout->loadView(guest/partial_quotes_table, [quotes => $open_quotes])}
@endif else {

            <div class="alert text-success no-margin">@lang('no_quotes_requiring_approval')</div>
            @endif

        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">@lang('overdue_invoices')</div>
        <div class="panel-body no-padding">
            @if($overdue_invoices)
{$this->layout->loadView(guest/partial_invoices_table, [invoices => $overdue_invoices])}
@endif else {

            <div class="alert text-success no-margin">@lang('no_overdue_invoices')</div>
            @endif

        </div>
    </div>

    <div class="panel panel-default">

        <div class="panel-heading">@lang('open_invoices')</div>

        <div class="panel-body no-padding">

            @if($overdue_invoices)
{$this->layout->loadView(guest/partial_invoices_table, [invoices => $open_invoices])}
@endif else {

            <div class="alert text-success no-margin">@lang('no_open_invoices')</div>
                @endif

        </div>

    </div>

</div>
