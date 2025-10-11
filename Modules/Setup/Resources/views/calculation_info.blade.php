
<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post" action="{{ url($this->uri->uri_string()) " }}>

            @csrf

            <h2>@lang('setup_calculation_info')</h2>

            <p>
                @lang('setup_calculation_info_message')
            </p>

            <p class="p-4 mb-4 text-yellow-700 dark:text-yellow-200 bg-yellow-100 dark:bg-yellow-900/50 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                @lang('setup_calculation_info_note')
            </p>

            <input type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors" name="btn_agree"
                   value="@lang('setup_calculation_info_btn_agree')">

            <input type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-600 dark:bg-yellow-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-yellow-700 dark:hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors" name="btn_continue"
                   value="@lang('setup_calculation_info_btn_disagree')">

        </form>
    </div>
</div>
