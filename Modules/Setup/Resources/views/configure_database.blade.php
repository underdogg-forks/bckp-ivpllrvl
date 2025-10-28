
<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post" action="{{ url($this->uri->uri_string());
?>">

            @csrf

            <legend>@lang('setup_database_details')</legend>

            @if(!$database['success'])
                @if($database['message'] && $_POST)
                    <div class="p-4 mb-4 text-red-700 dark:text-red-200 bg-red-100 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg">
                        <b>@php
        @lang('failure')</b><br>
                        {{ $database['message'] }}
                    </div>
                @endif

                <p>@lang('setup_database_message')</p>

                <div class="mb-4">
        <label for="db_hostname">
            @lang('hostname')
        </label>
        <input type="text" name="db_hostname" id="db_hostname" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
               value="{{ $this->input->post('db_hostname') ? $this->input->post('db_hostname') : 'localhost' " }}>
        <span class="help-block">@lang('setup_db_hostname_info')</span>
    </div>

    <div class="mb-4">
        <label for="db_port">
            @lang('port')
        </label>
        <input type="text" name="db_port" id="db_port" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
               value="{{ $this->input->post('db_port') ? $this->input->post('db_port') : 3306 " }}>
        <span class="help-block">@lang('setup_db_port_info')</span>
    </div>

    <div class="mb-4">
        <label>
            @lang('username')
        </label>
        <input type="text" name="db_username" id="db_username" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
               value="{{ $this->input->post('db_username') " }}>
        <span class="help-block">@lang('setup_db_username_info')</span>
    </div>

    <div class="mb-4">
        <label>
            @lang('password')
        </label>
        <input type="password" name="db_password" id="db_password" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
               value="{{ $this->input->post('db_password') " }}>
        <span class="help-block">@lang('setup_db_password_info')</span>
    </div>

    <div class="mb-4">
        <label>
            @lang('database')
        </label>
        <input type="text" name="db_database" id="db_database" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
               value="{{ $this->input->post('db_database') " }}>
        <span class="help-block">@lang('setup_db_database_info')</span>
    </div>
    @endif

    @if($errors)
    <input type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" name="btn_try_again"
           value="@lang('try_again')">
    @else
    <p><i class="fa fa-check text-success fa-margin"></i>
        @lang('setup_database_configured_message')
    </p>
    <input type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors" name="btn_continue"
           value="@lang('continue')">
        @endif

    </form>

</div>
</div>
