
<div id="headerbar">
    <h1 class="headerbar-title">@lang('import_data')</h1>
</div>

<div id="content">

    <div class="flex flex-wrap -mx-4">
        <div class="w-full px-4 md:w-1/2 col-md-offset-3">

            @include('layout.alerts')

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <h5>@lang('import_from_csv')</h5>
                </div>

                <div class="p-6">
                    <form method="post" action="{{ url($this->uri->uri_string()) " }}>

                        {{ _csrf_field() }}foreach ($files as $file) {

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="files[]" value="{{ $file " }}>
                                {{ $file }}
                            </label>
                        </div>
                            @endif
                        <input type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" name="btn_submit" value="@lang('import')">

                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
