
<form method="post">

    @csrf

    <div id="headerbar">
        <h1 class="headerbar-title">@lang('invoice_group_form')</h1>
        @include('layout.header_buttons')
    </div>

    <div id="content">

        <div class="flex flex-wrap -mx-4">
            <div class="w-full px-4 md:w-1/2 col-md-offset-3">

                @include('layout.alerts')

                <div class="mb-4">
                    <label class="control-label" for="invoice_group_name">
                        @lang('name')
                    </label>
                    <input type="text" name="invoice_group_name" id="invoice_group_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                           value="{{ $this->mdl_invoice_groups->form_value('invoice_group_name', true) }}" required>
                </div>

                <div class="mb-4">
                    <label class="control-label" for="invoice_group_identifier_format">
                        @lang('identifier_format')
                    </label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors taggable"
                           name="invoice_group_identifier_format" id="invoice_group_identifier_format"
                           value="{{ $this->mdl_invoice_groups->form_value('invoice_group_identifier_format', true) }}"
                           placeholder="INV-{{{id}}}" required>
                </div>

                <div class="mb-4">
                    <label class="control-label" for="invoice_group_next_id">
                        @lang('next_id')
                    </label>
                    <input type="number" name="invoice_group_next_id" id="invoice_group_next_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                           value="{{ $this->mdl_invoice_groups->form_value('invoice_group_next_id') }}" required>
                </div>

                <div class="mb-4">
                    <label class="control-label" for="invoice_group_left_pad">
                        @lang('left_pad')
                    </label>
                    <input type="number" name="invoice_group_left_pad" id="invoice_group_left_pad" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                           value="{{ $this->mdl_invoice_groups->form_value('invoice_group_left_pad') }}" required>
                </div>

                <hr>

                <div class="mb-4 no-margin">

                    <label for="tags_client">@lang('identifier_format_template_tags')</label>

                    <p class="small">@lang('identifier_format_template_tags_instructions')</p>

                    <select id="tags_client" class="tag-select w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors">
                        <option value="{{{id}" }}>
                            @lang('id')
                        </option>
                        <option value="{{{year}" }}>
                            @lang('current_year')
                        </option>
                        <option value="{{{yy}" }}>
                            @lang('current_yy')
                        </option>
                        <option value="{{{month}" }}>
                            @lang('current_month')
                        </option>
                        <option value="{{{day}" }}>
                            @lang('current_day')
                        </option>
                    </select>

                </div>

            </div>
        </div>

    </div>

</form>
