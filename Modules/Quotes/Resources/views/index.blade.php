
<div id="headerbar">

    <h1 class="headerbar-title">@lang('quotes')</h1>

    <div class="headerbar-item float-right">
        <button type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5 submenu-toggle lg:hidden"
                data-toggle="collapse" data-target="#ip-submenu-collapse">
            <i class="fa fa-bars"></i> @lang('submenu')
        </button>
        <a class="create-quote inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" href="#">
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>

    <div class="headerbar-item float-right hidden lg:block">
        {{ pager(site_url('quotes/status/' . $this->uri->segment(3)), 'mdl_quotes') }}
    </div>

    <div class="headerbar-item float-right hidden lg:block">
        <div class="inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm index-options">
            <a href="{{ url('quotes/status/all') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$status == 'all'? 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500'}}">
                @lang('all')
            </a>
            <a href="{{ url('quotes/status/draft') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$status == 'draft'? 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500'}}">
                @lang('draft')
            </a>
            <a href="{{ url('quotes/status/sent') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$status == 'sent'? 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500'}}">
                @lang('sent')
            </a>
            <a href="{{ url('quotes/status/viewed') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$status == 'viewed'? 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500'}}">
                @lang('viewed')
            </a>
            <a href="{{ url('quotes/status/approved') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$status == 'approved'? 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500'}}">
                @lang('approved')
            </a>
            <a href="{{ url('quotes/status/rejected') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$status == 'rejected'? 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500'}}">
                @lang('rejected')
            </a>
            <a href="{{ url('quotes/status/canceled') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$status == 'canceled'? 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500'}}">
                @lang('canceled')
            </a>
        </div>
    </div>

</div>

<div id="submenu">
    <div class="collapse clear-both" id="ip-submenu-collapse">

        <div class="submenu-row">
            {{ pager(site_url('quotes/status/' . $this->uri->segment(3)), 'mdl_quotes') }}
        </div>

        <div class="submenu-row">
            <div class="inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm index-options">
                <a href="{{ url('quotes/status/all') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$status == 'all'? 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500'}}">
                    @lang('all')
                </a>
                <a href="{{ url('quotes/status/draft') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$status == 'draft'? 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500'}}">
                    @lang('draft')
                </a>
                <a href="{{ url('quotes/status/sent') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$status == 'sent'? 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500'}}">
                    @lang('sent')
                </a>
                <a href="{{ url('quotes/status/viewed') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$status == 'viewed'? 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500'}}">
                    @lang('viewed')
                </a>
                <a href="{{ url('quotes/status/approved') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$status == 'approved'? 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500'}}">
                    @lang('approved')
                </a>
                <a href="{{ url('quotes/status/rejected') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$status == 'rejected'? 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500'}}">
                    @lang('rejected')
                </a>
                <a href="{{ url('quotes/status/canceled') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$status == 'canceled'? 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500'}}">
                    @lang('canceled')
                </a>
            </div>
        </div>

    </div>
</div>

<div id="content" class="table-content">

    <div id="filter_results">
        @include('quotes.partial_quote_table')
    </div>

</div>
