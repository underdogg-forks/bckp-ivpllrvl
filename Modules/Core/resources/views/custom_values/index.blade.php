
<div id="headerbar">
    <h1 class="headerbar-title">@lang('custom_values')</h1>

    <div class="headerbar-item float-right">
        <div class="inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm">
            <a class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" href="{{ url('custom_fields') " }}>
                <i class="fa fa-arrow-left"></i> @lang('back')
            </a>
            <a class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" href="{{ url('custom_fields/form') " }}>
                <i class="fa fa-plus"></i> @lang('new')
            </a>
        </div>
    </div>

    <div class="headerbar-item float-right">
        {{ pager(site_url('custom_values/index'), 'mdl_custom_values') }}
    </div>
</div>

<div id="content" class="table-content">

    {{ $this->layout->loadView('layout/alerts') }}

    <div id="filter_results">
        @include('custom_values.partial_custom_values_table')
    </div>

</div>
