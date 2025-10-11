
<div class="flex flex-wrap -mx-4">
    <div class="w-full px-4 col-md-8 col-md-offset-2">

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                @lang('quote')
            </div>
            <div class="p-6">

                <div class="flex flex-wrap -mx-4">
                    <div class="w-full px-4 md:w-1/2">

                        <div class="mb-4">
                            <label for="settings[default_quote_group]">
                                @lang('default_quote_group')
                            </label>
                            <select name="settings[default_quote_group]" id="settings[default_quote_group]"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select" data-minimum-results-for-search="Infinity">
                                <option value="">@lang('none')</option>
                                @foreach($invoice_groups as $invoice_group)
                                <option value="{{ $invoice_group->invoice_group_id }}"
                                    @php
                                        check_select(get_setting('default_quote_group'), $invoice_group->invoice_group_id)>
                                    {{ $invoice_group->invoice_group_name }}
                                </option>@endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="settings[default_quote_notes]">
                                @lang('default_notes')
                            </label>
                            <textarea name="settings[default_quote_notes]" id="settings[default_quote_notes]" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors">{{ get_setting('default_quote_notes', '', true) }}</textarea>
                        </div>

                    </div>
                    <div class="w-full px-4 md:w-1/2">

                        <div class="mb-4">
                            <label for="settings[quotes_expire_after]">
                                @lang('quotes_expire_after')
                            </label>
                            <input type="number" name="settings[quotes_expire_after]" id="settings[quotes_expire_after]"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                   value="{{ get_setting('quotes_expire_after') " }}>
                        </div>

                        <div class="mb-4">
                            <label for="settings[generate_quote_number_for_draft]">
                                @lang('generate_quote_number_for_draft')
                            </label>
                            <select name="settings[generate_quote_number_for_draft]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select"
                                    id="settings[generate_quote_number_for_draft]"
                                    data-minimum-results-for-search="Infinity">
                                <option value="0">
                                    @lang('no')
                                </option>
                                <option
                                    value="1" @php check_select(get_setting('generate_quote_number_for_draft'), '1')>
                                    @lang('yes')
                                </option>
                            </select>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                @lang('pdf_settings')
            </div>
            <div class="p-6">
                <div class="flex flex-wrap -mx-4">
                    <div class="w-full px-4 md:w-1/2">

                        <div class="mb-4">
                            <label for="settings[mark_quotes_sent_pdf]">
                                @lang('mark_quotes_sent_pdf')
                            </label>
                            <select name="settings[mark_quotes_sent_pdf]" id="settings[mark_quotes_sent_pdf]"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select" data-minimum-results-for-search="Infinity">
                                <option value="0">
                                    @lang('no')
                                </option>
                                <option value="1" @php check_select(get_setting('mark_quotes_sent_pdf'), '1')>
                                    @lang('yes')
                                </option>
                            </select>
                        </div>

                    </div>
                    <div class="w-full px-4 md:w-1/2">

                        <div class="mb-4">
                            <label for="settings[quote_pre_password]">
                                @lang('quote_pre_password')
                            </label>
                            <input type="text" name="settings[quote_pre_password]" id="settings[quote_pre_password]"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors" value="{{ get_setting('quote_pre_password', '', true) " }}>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                @lang('quote_templates')
            </div>
            <div class="p-6">

                <div class="flex flex-wrap -mx-4">
                    <div class="w-full px-4 md:w-1/2">

                        <div class="mb-4">
                            <label for="settings[pdf_quote_template]">
                                @lang('default_pdf_template')
                            </label>
                            <select name="settings[pdf_quote_template]" id="settings[pdf_quote_template]"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select" data-minimum-results-for-search="Infinity">
                                <option value="">@lang('none')</option>
                                @foreach($pdf_quote_templates as $quote_template)
                                <option value="{{ $quote_template }}"
                                    @php
                                        check_select(get_setting('pdf_quote_template'), $quote_template)>
                                    {{ $quote_template }}
                                </option>@endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="settings[public_quote_template]">
                                @lang('default_public_template')
                            </label>
                            <select name="settings[public_quote_template]" id="settings[public_quote_template]"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select" data-minimum-results-for-search="Infinity">
                                <option value="">@lang('none')</option>
                                @foreach($public_quote_templates as $quote_template)
                                <option value="{{ $quote_template }}"
                                    @php
                                        check_select(get_setting('public_quote_template'), $quote_template)>
                                    {{ $quote_template }}
                                </option>@endforeach
                            </select>
                        </div>

                    </div>
                    <div class="w-full px-4 md:w-1/2">

                        <div class="mb-4">
                            <label for="settings[email_quote_template]">
                                @lang('default_email_template')
                            </label>
                            <select name="settings[email_quote_template]" id="settings[email_quote_template]"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select" data-minimum-results-for-search="Infinity">
                                <option value="">@lang('none')</option>
                                @foreach($email_templates_quote as $email_template)
                                <option value="{{ $email_template->email_template_id }}"
                                    @php
                                        check_select(get_setting('email_quote_template'), $email_template->email_template_id)>
                                    {{ $email_template->email_template_title }}
                                </option>@endforeach
                            </select>
                        </div>

                    </div>
                </div>

                <div class="flex flex-wrap -mx-4">
                    <div class="w-full px-4 md:w-1/2">

                        <div class="mb-4">
                            <label for="settings[pdf_quote_footer]">
                                @lang('pdf_quote_footer')
                            </label>
                            <textarea name="settings[pdf_quote_footer]" id="settings[pdf_quote_footer]"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors no-margin">{{ get_setting('pdf_quote_footer', '', true) }}</textarea>
                            <p class="help-block">@lang('pdf_quote_footer_hint')</p>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
