
<div id="headerbar">
    <h1 class="headerbar-title">@lang('invoice_aging')</h1>
</div>

<div id="content">

    <div class="flex flex-wrap -mx-4">
        <div class="w-full px-4 md:w-1/2 col-md-offset-3">

            @include('layout.alerts')

            <div id="report_options" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">

                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <i class="fa fa-print"></i>
                    @lang('report_options')
                </div>

                <div class="p-6">
                    <form method="post" action="{{ url($this->uri->uri_string()) }}"
                        {{ get_setting('reports_in_new_tab', false) ? 'target="_blank"' : '' }}>

                        @csrf

                        <input type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
                               name="btn_submit" value="@lang('run_report')">

                    </form>
                </div>

            </div>

        </div>
    </div>

</div>
