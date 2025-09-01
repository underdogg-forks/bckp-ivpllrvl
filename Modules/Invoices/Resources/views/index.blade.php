
<div id="headerbar">

    <h1 class="headerbar-title">@lang('invoices')</h1>

    <div class="headerbar-item pull-right">
        <button type="button" class="btn btn-default btn-sm submenu-toggle hidden-lg"
                data-toggle="collapse" data-target="#ip-submenu-collapse">
            <i class="fa fa-bars"></i> @lang('submenu')
        </button>
        <a class="create-invoice btn btn-sm btn-primary" href="#">
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>

    <div class="headerbar-item pull-right visible-lg">
        {{ pager(site_url('invoices/status/' . $this->uri->segment(3)), 'mdl_invoices') }}
    </div>

    <div class="headerbar-item pull-right visible-lg">
        <div class="btn-group btn-group-sm index-options">
            <a href="{{ url('invoices/status/all') }}"
               class="btn {{ $status == 'all' ? 'btn-primary' : 'btn-default' }}">
                @lang('all')
            </a>
            <a href="{{ url('invoices/status/draft') }}"
               class="btn {{ $status == 'draft' ? 'btn-primary' : 'btn-default' }}">
                @lang('draft')
            </a>
            <a href="{{ url('invoices/status/sent') }}"
               class="btn {{ $status == 'sent' ? 'btn-primary' : 'btn-default' }}">
                @lang('sent')
            </a>
            <a href="{{ url('invoices/status/viewed') }}"
               class="btn {{ $status == 'viewed' ? 'btn-primary' : 'btn-default' }}">
                @lang('viewed')
            </a>
            <a href="{{ url('invoices/status/paid') }}"
               class="btn {{ $status == 'paid' ? 'btn-primary' : 'btn-default' }}">
                @lang('paid')
            </a>
            <a href="{{ url('invoices/status/overdue') }}"
               class="btn {{ $status == 'overdue' ? 'btn-primary' : 'btn-default' }}">
                @lang('overdue')
            </a>
        </div>
    </div>

</div>

<div id="submenu">
    <div class="collapse clearfix" id="ip-submenu-collapse">

        <div class="submenu-row">
            {{ pager(site_url('invoices/status/' . $this->uri->segment(3)), 'mdl_invoices') }}
        </div>

        <div class="submenu-row">
            <div class="btn-group btn-group-sm index-options">
                <a href="{{ url('invoices/status/all') }}"
                   class="btn {{ $status == 'all' ? 'btn-primary' : 'btn-default' }}">
                    @lang('all')
                </a>
                <a href="{{ url('invoices/status/draft') }}"
                   class="btn  {{ $status == 'draft' ? 'btn-primary' : 'btn-default' }}">
                    @lang('draft')
                </a>
                <a href="{{ url('invoices/status/sent') }}"
                   class="btn  {{ $status == 'sent' ? 'btn-primary' : 'btn-default' }}">
                    @lang('sent')
                </a>
                <a href="{{ url('invoices/status/viewed') }}"
                   class="btn  {{ $status == 'viewed' ? 'btn-primary' : 'btn-default' }}">
                    @lang('viewed')
                </a>
                <a href="{{ url('invoices/status/paid') }}"
                   class="btn  {{ $status == 'paid' ? 'btn-primary' : 'btn-default' }}">
                    @lang('paid')
                </a>
                <a href="{{ url('invoices/status/overdue') }}"
                   class="btn  {{ $status == 'overdue' ? 'btn-primary' : 'btn-default' }}">
                    @lang('overdue')
                </a>
            </div>
        </div>

    </div>
</div>

<div id="content" class="table-content">
    <div id="filter_results">
        @include('invoices.partial_invoice_table')
    </div>
</div>
