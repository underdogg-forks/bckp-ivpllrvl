@php namespace Modules\Guest\Views; @endphp
<div id="headerbar">

    <h1 class="headerbar-title">@lang('invoices')</h1>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('guest/invoices/status/' . $this->uri->segment(4)), 'mdl_invoices') }}
    </div>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm index-options">
            <a href="{{ url('guest/invoices/status/open') }}"
               class="btn {{ $status == 'open' ? 'btn-primary' : 'btn-default' }}">
                @lang('open')
            </a>
            <a href="{{ url('guest/invoices/status/overdue') }}"
               class="btn {{ $status == 'overdue' ? 'btn-primary' : 'btn-default' }}">
                @lang('overdue')
            </a>
            <a href="{{ url('guest/invoices/status/paid') }}"
               class="btn  {{ $status == 'paid' ? 'btn-primary' : 'btn-default' }}">
                @lang('paid')
            </a>
            <a href="{{ url('guest/invoices/status/all') }}"
               class="btn  {{ $status == 'all' ? 'btn-primary' : 'btn-default' }}">
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
