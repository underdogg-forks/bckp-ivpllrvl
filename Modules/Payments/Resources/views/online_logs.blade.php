
<div id="headerbar">
    <h1 class="headerbar-title">@lang('payment_logs')</h1>

    <div class="headerbar-item float-right">
        {{ pager(site_url('payments/online_logs'), 'mdl_payment_logs') }}
    </div>

</div>

<div id="content" class="table-content">

    @include('layout.alerts')

    <div id="filter_results">
        @include('payments.partial_online_logs_table')
    </div>

</div>
