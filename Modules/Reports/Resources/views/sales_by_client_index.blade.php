
<div id="headerbar">
    <h1 class="headerbar-title">@lang('sales_by_client')</h1>
</div>

<div id="content">

    <div class="flex flex-wrap -mx-4">
        <div class="w-full px-4 md:w-1/2 md:mx-auto">

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

                        <div class="mb-4 has-feedback">
                            <label for="from_date">
                                @lang('from_date')
                            </label>

                            <div class="input-group">
                                <input name="from_date" id="from_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors datepicker">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar fa-fw"></i>
                            </span>
                            </div>
                        </div>

                        <div class="mb-4 has-feedback">
                            <label for="to_date">
                                @lang('to_date')
                            </label>

                            <div class="input-group">
                                <input name="to_date" id="to_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors datepicker">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar fa-fw"></i>
                            </span>
                            </div>
                        </div>

                        <input type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors" name="btn_submit"
                               value="@lang('run_report')">

                    </form>

                </div>

            </div>

        </div>
    </div>

</div>
