
<form method="post" class="space-y-4">

    @csrf

    <div id="headerbar">
        <h1 class="headerbar-title">@lang('tax_rate_form')</h1>
        @include('layout.header_buttons')
    </div>

    <div id="content">

        @include('layout.alerts')

        <div class="mb-4">
            <div class="w-full px-4 col-sm-2 text-right text-left -xs">
                <label class="control-label">
                    @lang('tax_rate_name')
                </label>
            </div>
            <div class="w-full px-4 col-sm-6">
                <input type="text" name="tax_rate_name" id="tax_rate_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                       value="{{ $this->mdl_tax_rates->form_value('tax_rate_name', true) }}" required>
            </div>
        </div>

        <div class="mb-4 has-feedback">
            <div class="w-full px-4 col-sm-2 text-right text-left -xs">
                <label class="control-label">
                    @lang('tax_rate_percent')
                </label>
            </div>
            <div class="w-full px-4 col-sm-6">
                <input type="text" name="tax_rate_percent" id="tax_rate_percent" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                       value="{{ format_amount($this->mdl_tax_rates->form_value('tax_rate_percent')) }}" required>
                <span class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors -feedback">%</span>
            </div>
        </div>

    </div>

</form>
