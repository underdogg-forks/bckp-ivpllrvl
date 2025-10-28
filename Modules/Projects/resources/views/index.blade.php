
<div id="headerbar">
    <h1 class="headerbar-title">@lang('projects')</h1>

    <div class="headerbar-item float-right">
        <a class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" href="{{ url('projects/form') " }}>
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>

    <div class="headerbar-item float-right">
        {{ pager(site_url('projects/index'), 'mdl_projects') }}
    </div>

</div>

<div id="content" class="table-content">

    @include('layout.alerts')

    <div id="filter_results">
        @include('projects.partial_projects_table')
    </div>

</div>
