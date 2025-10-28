
<div id="headerbar">
    <h1 class="headerbar-title">@lang('recurring_invoices')</h1>

    <div class="headerbar-item float-right">
        {{ pager(site_url('invoices/recurring/index'), 'mdl_invoices_recurring') }}
    </div>
</div>

<div id="content" class="table-content">
    <div id="filter_results">
        @include('invoices.partial_invoices_recurring_table')
    </div>
</div>
