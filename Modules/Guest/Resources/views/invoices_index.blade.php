
<div id="headerbar">

    <h1 class="headerbar-title">@lang('invoices')</h1>

    <div class="headerbar-item float-right">
        {{ pager(site_url('guest/invoices/status/' . $this->uri->segment(4)), 'mdl_invoices') }}
    </div>

    <div class="headerbar-item float-right">
        <div class="inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm index-options">
            <a href="{{ url('guest/invoices/status/open') }}"
               class="btn {{ $status == 'open' ? 'btn-primary' : '-default'" }}>
                @lang('open')
            </a>
            <a href="{{ url('guest/invoices/status/overdue') }}"
               class="btn {{ $status == 'overdue' ? 'btn-primary' : '-default'" }}>
                @lang('overdue')
            </a>
            <a href="{{ url('guest/invoices/status/paid') }}"
               class="btn {{ $status == 'paid' ? 'btn-primary' : '-default'" }}>
                @lang('paid')
            </a>
            <a href="{{ url('guest/invoices/status/all') }}"
               class="btn {{ $status == 'all' ? 'btn-primary' : '-default'" }}>
                @lang('all')
            </a>
        </div>
    </div>

</div>

<div id="content" class="table-content">

    {{ $this->layout->loadView('layout/alerts') }}

    <div id="filter_results">

        {{ $this->layout->loadView('guest/partial_invoices_table') }}

    </div>

</div>
