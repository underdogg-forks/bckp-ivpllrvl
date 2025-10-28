<div class="flex flex-wrap -mx-4">
    <div class="w-full px-4 col-md-8 col-md-offset-2">

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                @lang('taxes')
            </div>

            <div class="p-6">
                <div class="flex flex-wrap -mx-4">
                    <div class="w-full px-4 md:w-1/2">

                        <div class="mb-4">
                            <label for="settings[default_invoice_tax_rate]">
                                @lang('default_invoice_tax_rate')
                            </label>

                            <select
                                name="settings[default_invoice_tax_rate]"
                                id="settings[default_invoice_tax_rate]"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select"
                            >
                                <option value="">@lang('none')</option>
                                @foreach($tax_rates as $tax_rate)
                                    <option
                                        value="{{ $tax_rate->tax_rate_id }}"
                                        @selected(get_setting('default_invoice_tax_rate') == $tax_rate->tax_rate_id)
                                    >
                                        {{ $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="settings[default_item_tax_rate]">
                                @lang('default_item_tax_rate')
                            </label>

                            <select
                                name="settings[default_item_tax_rate]"
                                id="settings[default_item_tax_rate]"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select"
                            >
                                <option value="">@lang('none')</option>
                                @foreach($tax_rates as $tax_rate)
                                    <option
                                        value="{{ $tax_rate->tax_rate_id }}"
                                        @selected(get_setting('default_item_tax_rate') == $tax_rate->tax_rate_id)
                                    >
                                        {{ $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if (!$legacy_calculation)
                        <input
                            type="hidden"
                            name="settings[default_include_item_tax]"
                            id="settings[default_include_item_tax]"
                            value=""
                        >
                    @else
                        <div class="w-full px-4 md:w-1/2">
                            <div class="mb-4">
                                <label for="settings[default_include_item_tax]">
                                    @lang('default_invoice_tax_rate_placement')
                                </label>

                                <select
                                    name="settings[default_include_item_tax]"
                                    id="settings[default_include_item_tax]"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select"
                                    data-minimum-results-for-search="Infinity"
                                >
                                    <option value="">@lang('none')</option>
                                    <option
                                        value="0"
                                        @selected(get_setting('default_include_item_tax') == '0')
                                    >
                                        @lang('apply_before_item_tax')
                                    </option>
                                    <option
                                        value="1"
                                        @selected(get_setting('default_include_item_tax') == '1')
                                    >
                                        @lang('apply_after_item_tax')
                                    </option>
                                </select>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
