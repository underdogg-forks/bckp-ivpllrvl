
<div id="headerbar">

    <h1 class="headerbar-title">@lang('invoices')</h1>

    <div class="headerbar-item float-right">
        {{ pager(site_url('guest/invoices/status/' . $this->uri->segment(4)), 'mdl_invoices') }}
    </div>

    <div class="headerbar-item float-right">
        <div class="inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm index-options">
            <a href="{{ url('guest/invoices/status/open') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$status == 'open'? 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500'}}">
                @lang('open')
            </a>
            <a href="{{ url('guest/invoices/status/overdue') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$status == 'overdue'? 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500'}}">
                @lang('overdue')
            </a>
            <a href="{{ url('guest/invoices/status/paid') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$status == 'paid'? 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500'}}">
                @lang('paid')
            </a>
            <a href="{{ url('guest/invoices/status/all') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$status == 'all'? 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500'}}">
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
