
<div class="flex flex-wrap -mx-4">
    <div class="w-full px-4 col-md-8 col-md-offset-2">

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                @lang('projects')
            </div>
            <div class="p-6">

                <div class="flex flex-wrap -mx-4">
                    <div class="w-full px-4 md:w-1/2">

                        <div class="mb-4">
                            <label for="settings[projects_enabled]">
                                @lang('enable_projects')
                            </label>
                            <select name="settings[projects_enabled]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select"
                                    id="settings[projects_enabled]" data-minimum-results-for-search="Infinity">
                                <option value="0">
                                    @lang('no')
                                </option>
                                <option value="1" @php check_select(get_setting('projects_enabled'), '1')>
                                    @lang('yes')
                                </option>
                            </select>
                        </div>

                    </div>
                    <div class="w-full px-4 md:w-1/2">

                        <div class="mb-4">
                            <label for="settings[default_hourly_rate]">
                                @lang('default_hourly_rate')
                            </label>
                            <div class="input-group">
                                <input type="text" name="settings[default_hourly_rate]"
                                       id="settings[default_hourly_rate]"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors amount"
                                       value="{{ get_setting('default_hourly_rate') ? format_amount(get_setting('default_hourly_rate')) : get_setting('default_hourly_rate') " }}>
                                <span class="input-group-addon">{{ get_setting('currency_symbol') }}</span>
                                <input type="hidden" name="settings[default_hourly_rate_field_is_amount]" value="1">
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
