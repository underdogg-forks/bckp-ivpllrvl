
<div id="headerbar">

    <h1 class="headerbar-title">@lang('clients')</h1>

    <div class="headerbar-item pull-right">
        <button type="button" class="btn btn-default btn-sm submenu-toggle hidden-lg"
                data-toggle="collapse" data-target="#ip-submenu-collapse">
            <i class="fa fa-bars"></i> @lang('submenu')
        </button>
        <a class="btn btn-primary btn-sm" href="{{ url('clients/form') }}">
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>

    <div class="headerbar-item pull-right visible-lg">
        {{ pager(site_url('clients/status/' . $this->uri->segment(3)), 'mdl_clients') }}
    </div>

    <div class="headerbar-item pull-right visible-lg">
        <div class="btn-group btn-group-sm index-options">
            <a href="{{ url('clients/status/active') }}"
               class="btn {{ $this->uri->segment(3) == 'active' || !$this->uri->segment(3) ? 'btn-primary' : 'btn-default' }}">
                @lang('active')
            </a>
            <a href="{{ url('clients/status/inactive') }}"
               class="btn  {{ $this->uri->segment(3) == 'inactive' ? 'btn-primary' : 'btn-default' }}">
                @lang('inactive')
            </a>
            <a href="{{ url('clients/status/all') }}"
               class="btn  {{ $this->uri->segment(3) == 'all' ? 'btn-primary' : 'btn-default' }}">
                @lang('all')
            </a>
        </div>
    </div>

</div>

<div id="submenu">
    <div class="collapse clearfix" id="ip-submenu-collapse">

        <div class="submenu-row">
            {{ pager(site_url('clients/status/' . $this->uri->segment(3)), 'mdl_clients') }}
        </div>

        <div class="submenu-row">
            <div class="btn-group btn-group-sm index-options">
                <a href="{{ url('clients/status/active') }}"
                   class="btn {{ $this->uri->segment(3) == 'active' || !$this->uri->segment(3) ? 'btn-primary' : 'btn-default' }}">
                    @lang('active')
                </a>
                <a href="{{ url('clients/status/inactive') }}"
                   class="btn  {{ $this->uri->segment(3) == 'inactive' ? 'btn-primary' : 'btn-default' }}">
                    @lang('inactive')
                </a>
                <a href="{{ url('clients/status/all') }}"
                   class="btn  {{ $this->uri->segment(3) == 'all' ? 'btn-primary' : 'btn-default' }}">
                    @lang('all')
                </a>
            </div>
        </div>

    </div>
</div>

<div id="content" class="table-content">

    @include('layout.alerts')

    <div id="filter_results">
        @include('clients.partial_client_table')
    </div>

</div>
