
<div id="headerbar">
    <h1 class="headerbar-title">@lang('users')</h1>

    <div class="headerbar-item float-right">
        <a class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" href="{{ url('users/form') " }}>
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>

    <div class="headerbar-item float-right">
        {{ pager(site_url('users/index'), 'mdl_users') }}
    </div>

</div>

<div id="content" class="table-content">

    {{ $this->layout->loadView('layout/alerts') }}

    <div id="filter_results">
        @include('users.partial_users_table')
    </div>

</div>
