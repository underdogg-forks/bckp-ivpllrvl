
<div id="headerbar">

    <h1 class="headerbar-title">@lang('invoices')</h1>

    <div class="headerbar-item float-right">
        <a class="create-invoice inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" href="#">
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>

    <div class="headerbar-item float-right">
        {{ pager(site_url('invoices/client/' . $client_id . '/' . $status), 'mdl_invoices') }}
    </div>

    <div class="headerbar-item float-right">
        <div class="inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm index-options">
            <a href="{{ url('invoices/client/' . $client_id . '/open') }}"
               class="btn {{ $status == 'open' ? 'btn-primary' : '-default'" }}>
                @lang('open')
            </a>
            <a href="{{ url('invoices/client/' . $client_id . '/closed') }}"
               class="btn {{ $status == 'closed' ? 'btn-primary' : '-default'" }}>
                @lang('closed')
            </a>
            <a href="{{ url('invoices/client/' . $client_id . '/overdue') }}"
               class="btn {{ $status == 'overdue' ? 'btn-primary' : '-default'" }}>
                @lang('overdue')
            </a>
        </div>
    </div>

</div>

<div id="content" class="table-content">

    @include('invoices/partial_invoice_table', ['invoices' => $invoices])

</div>
