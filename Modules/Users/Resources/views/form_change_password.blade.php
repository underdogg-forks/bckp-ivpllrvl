
<script src="{{ _core_asset('js/zxcvbn.js') " }}></script>

<form method="post">

    @csrf

    <div id="headerbar">
        <h1 class="headerbar-title">@lang('change_password')</h1>
        {{ $this->layout->loadView('layout/header_buttons') }}
    </div>

    <div id="content">

        <div class="flex flex-wrap -mx-4">
            <div class="w-full px-4 md:w-1/2 col-md-offset-3">

                @include('layout.alerts')

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        @lang('change_password')
                    </div>

                    <div class="p-6">
                        <div class="mb-4">
                            <label for="user_password">
                                @lang('password')
                            </label>
                            <input type="password" name="user_password" id="user_password"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors passwordmeter-input" required>
                            <div class="progress" style="height:3px;">
                                <div class="progress-bar progress-bar-danger passmeter passmeter-1"
                                     style="width: 33%"></div>
                                <div class="progress-bar progress-bar-warning passmeter passmeter-2"
                                     style="display: none; width: 33%"></div>
                                <div class="progress-bar progress-bar-success passmeter passmeter-3"
                                     style="display: none; width: 34%"></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="user_passwordv">
                                @lang('verify_password')
                            </label>
                            <input type="password" name="user_passwordv" id="user_passwordv"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors" required>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

</form>
