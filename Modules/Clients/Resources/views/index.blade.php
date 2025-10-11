
<div id="headerbar">

    <h1 class="headerbar-title">@lang('clients')</h1>

    <div class="headerbar-item float-right">
        <button type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5 submenu-toggle lg:hidden"
                data-toggle="collapse" data-target="#ip-submenu-collapse">
            <i class="fa fa-bars"></i> @lang('submenu')
        </button>
        <a class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5" href="{{ url('clients/form') " }}>
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>

    <div class="headerbar-item float-right hidden lg:block">
        {{ pager(site_url('clients/status/' . $this->uri->segment(3)), 'mdl_clients') }}
    </div>

    <div class="headerbar-item float-right hidden lg:block">
        <div class="inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm index-options">
            <a href="{{ url('clients/status/active') }}"
               class="btn {{ $this->uri->segment(3) == 'active' || !$this->uri->segment(3) ? 'btn-primary' : '-default'" }}>
                @lang('active')
            </a>
            <a href="{{ url('clients/status/inactive') }}"
               class="btn {{ $this->uri->segment(3) == 'inactive' ? 'btn-primary' : '-default'" }}>
                @lang('inactive')
            </a>
            <a href="{{ url('clients/status/all') }}"
               class="btn {{ $this->uri->segment(3) == 'all' ? 'btn-primary' : '-default'" }}>
                @lang('all')
            </a>
        </div>
    </div>

</div>

<div id="submenu">
    <div class="collapse clear-both" id="ip-submenu-collapse">

        <div class="submenu-row">
            {{ pager(site_url('clients/status/' . $this->uri->segment(3)), 'mdl_clients') }}
        </div>

        <div class="submenu-row">
            <div class="inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm index-options">
                <a href="{{ url('clients/status/active') }}"
                   class="btn {{ $this->uri->segment(3) == 'active' || !$this->uri->segment(3) ? 'btn-primary' : '-default'" }}>
                    @lang('active')
                </a>
                <a href="{{ url('clients/status/inactive') }}"
                   class="btn {{ $this->uri->segment(3) == 'inactive' ? 'btn-primary' : '-default'" }}>
                    @lang('inactive')
                </a>
                <a href="{{ url('clients/status/all') }}"
                   class="btn {{ $this->uri->segment(3) == 'all' ? 'btn-primary' : '-default'" }}>
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
