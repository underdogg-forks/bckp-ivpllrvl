
<form method="post">

    @csrf

    <div id="headerbar">
        <h1 class="headerbar-title">@lang('add_unit')</h1>
        @include('layout.header_buttons')
    </div>

    <div id="content">

        <div class="flex flex-wrap -mx-4">
            <div class="w-full px-4 md:w-1/2 col-md-offset-3">

                @include('layout.alerts')

                <input class="hidden" name="is_update" type="hidden"
                    @if($this->mdl_units->form_value('is_update'))
{value="1"}
@endif else {
    echo 'value="0"';
}
                >

                <div class="mb-4">
                    <label for="unit_name">
                        @lang('unit_name')
                    </label>
                    <input type="text" name="unit_name" id="unit_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                           value="{{ $this->mdl_units->form_value('unit_name', true) }}" required>
                </div>

                <div class="mb-4">
                    <label for="unit_name_plrl">
                        @lang('unit_name_plrl')
                    </label>
                    <input type="text" name="unit_name_plrl" id="unit_name_plrl" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                           value="{{ $this->mdl_units->form_value('unit_name_plrl', true) }}" required>
                </div>

            </div>
        </div>

    </div>

</form>
