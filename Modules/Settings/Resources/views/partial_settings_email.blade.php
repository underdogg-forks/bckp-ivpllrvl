
<script>
    $(function () {
        toggle_smtp_settings();

        $('#email_send_method').change(function () {
            toggle_smtp_settings();
        });

        function toggle_smtp_settings() {
            email_send_method = $('#email_send_method').val();

            @if(email_send_method === 'smtp') {
                $('#div-smtp-settings').show();
            } else {
                $('#div-smtp-settings').hide();
            }
        }
    });
</script>

<div class="flex flex-wrap -mx-4">
    <div class="w-full px-4 col-md-8 col-md-offset-2">

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                @lang('email')
            </div>
            <div class="p-6">

                <div class="flex flex-wrap -mx-4">
                    <div class="w-full px-4 md:w-1/2">

                        <div class="mb-4">
                            <label for="settings[email_pdf_attachment]">
                                @lang('email_pdf_attachment')
                            </label>
                            <select name="settings[email_pdf_attachment]" id="settings[email_pdf_attachment]"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select" data-minimum-results-for-search="Infinity">
                                <option value="0">@lang('no')</option>
                                <option value="1" @php check_select(get_setting('email_pdf_attachment'), '1')>
                                    @lang('yes')
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="email_send_method">
                                @lang('email_send_method')
                            </label>
                            <select name="settings[email_send_method]" id="email_send_method"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select" data-minimum-results-for-search="Infinity">
                                <option value="">@lang('none')</option>
                                <option
                                    value="phpmail" @php check_select(get_setting('email_send_method'), 'phpmail')>
                                    @lang('email_send_method_phpmail')
                                </option>
                                <option
                                    value="sendmail" @php check_select(get_setting('email_send_method'), 'sendmail')>
                                    @lang('email_send_method_sendmail')
                                </option>
                                <option
                                    value="smtp" @php check_select(get_setting('email_send_method'), 'smtp')>
                                    @lang('email_send_method_smtp')
                                </option>
                            </select>
                        </div>

                        <div id="div-smtp-settings">
                            <hr>

                            <div class="mb-4">
                                <label for="settings[smtp_server_address]">
                                    @lang('smtp_server_address')
                                </label>
                                <input type="text" name="settings[smtp_server_address]"
                                       id="settings[smtp_server_address]"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ get_setting('smtp_server_address', '', true) " }}>
                            </div>

                            <div class="mb-4">
                                <label for="settings[smtp_mail_from]">
                                    @lang('smtp_mail_from')
                                </label>
                                <input type="email" name="settings[smtp_mail_from]" id="settings[smtp_mail_from]"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ get_setting('smtp_mail_from', '', true) " }}>
                            </div>

                            <div class="mb-4">
                                <label for="settings[smtp_authentication]">
                                    @lang('smtp_requires_authentication')
                                </label>
                                <select name="settings[smtp_authentication]" id="settings[smtp_authentication]"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select">
                                    <option value="0">
                                        @lang('no')
                                    </option>
                                    <option
                                        value="1" @php check_select(get_setting('smtp_authentication'), '1')>
                                        @lang('yes')
                                    </option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="settings[smtp_username]">
                                    @lang('smtp_username')
                                </label>
                                <input type="text" name="settings[smtp_username]" id="settings[smtp_username]"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ get_setting('smtp_username', '', true) " }}>
                            </div>

                            <div class="mb-4">
                                <label for="smtp_password">
                                    @lang('smtp_password')
                                </label>
                                <input type="password" id="smtp_password" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       name="settings[smtp_password]" autocomplete="new-password"
                                       value="">
                                <input type="hidden" name="settings[smtp_password_field_is_password]" value="1">
                            </div>

                            <div class="mb-4">
                                <div>
                                    <label for="settings[smtp_port]">
                                        @lang('smtp_port')
                                    </label>
                                    <input type="number" name="settings[smtp_port]" id="settings[smtp_port]"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                           value="{{ get_setting('smtp_port') " }}>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="settings[smtp_security]">
                                    @lang('smtp_security')
                                </label>
                                <select name="settings[smtp_security]" id="settings[smtp_security]"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select" data-minimum-results-for-search="Infinity">
                                    <option value="">@lang('none')</option>
                                    <option value="ssl" @php check_select(get_setting('smtp_security'), 'ssl')>
                                        @lang('smtp_ssl')
                                    </option>
                                    <option value="tls" @php check_select(get_setting('smtp_security'), 'tls')>
                                        @lang('smtp_tls')
                                    </option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="settings[smtp_verify_certs]">
                                    @lang('smtp_verify_certs')
                                </label>
                                <select name="settings[smtp_verify_certs]" id="settings[smtp_verify_certs]"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select" data-minimum-results-for-search="Infinity">
                                    <option value="1">@lang('yes')</option>
                                    <option value="0" @php check_select(get_setting('smtp_verify_certs'), '0')>
                                        @lang('no')
                                    </option>
                                </select>
                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
