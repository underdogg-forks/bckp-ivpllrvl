$is_update = $this->mdl_families->form_value('is_update');
<form method="post">

    @csrf

    <div id="headerbar">
        <h1 class="headerbar-title">{{ _trans($is_update ? 'family' : 'add_family') }}</h1>
        @include('layout.header_buttons')
    </div>

    <div id="content">

        <div class="flex flex-wrap -mx-4">
            <div class="w-full px-4 md:w-1/2 col-md-offset-3">

                @include('layout.alerts')

                <input class="hidden" name="is_update" type="hidden" value="{{ $is_update ? '1' : '0' " }}>

                <div class="mb-4">
                    <label for="family_name">
                        @lang('family_name')
                    </label>
                    <input type="text" name="family_name" id="family_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                           value="{{ $this->mdl_families->form_value('family_name', true) }}" required>
                </div>

            </div>
        </div>

    </div>

</form>
