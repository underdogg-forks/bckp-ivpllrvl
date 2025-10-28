
<form method="post">

    {{ _csrf_field() }}?>

            <div id="headerbar">
                <h1 class="headerbar-title">@lang('email_template_form')</h1>
                @include('layout.header_buttons')
    </div>

    <div id="content">

        @include('layout.alerts')

        <input class="hidden" name="is_update" type="hidden"
               value="{{ $this->mdl_email_templates->form_value('is_update') ? '1' : '0' " }}>

        <div class="flex flex-wrap -mx-4">
            <div class="w-full px-4 col-md-8 col-md-offset-2">

                <div class="mb-4">
                    <label for="email_template_title" class="control-label">@lang('title')</label>
                    <input type="text" name="email_template_title" id="email_template_title"
                           value="{{ $this->mdl_email_templates->form_value('email_template_title', true) }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors" required>
                </div>

                <div class="mb-4">
                    <label for="email_template_type" class="control-label">@lang('type')</label>
                    <div class="radio">
                        <label>
                            <input type="radio" name="email_template_type" id="email_template_type_invoice"
                                   value="invoice" checked>
                            @lang('invoice')
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="email_template_type" id="email_template_type_quote"
                                   value="quote">
                            @lang('quote')
                        </label>
                    </div>
                </div>

                <hr>

                <div class="mb-4">
                    <label for="email_template_from_name" class="control-label">
                        @lang('from_name')
                    </label>
                    <input type="text" name="email_template_from_name" id="email_template_from_name"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors taggable"
                           value="{{ $this->mdl_email_templates->form_value('email_template_from_name', true) " }}>
                </div>

                <div class="mb-4">
                    <label for="email_template_from_email" class="control-label">
                        @lang('from_email')
                    </label>
                    <input type="text" name="email_template_from_email" id="email_template_from_email"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors taggable"
                           value="{{ $this->mdl_email_templates->form_value('email_template_from_email', true) " }}>
                </div>

                <div class="mb-4">
                    <label for="email_template_cc" class="control-label">@lang('cc')</label>
                    <input type="text" name="email_template_cc" id="email_template_cc" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors taggable"
                           value="{{ $this->mdl_email_templates->form_value('email_template_cc', true) " }}>
                </div>

                <div class="mb-4">
                    <label for="email_template_bcc" class="control-label">@lang('bcc'): </label>
                    <input type="text" name="email_template_bcc" id="email_template_bcc" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors taggable"
                           value="{{ $this->mdl_email_templates->form_value('email_template_bcc', true) " }}>
                </div>

                <div class="mb-4">
                    <label for="email_template_subject" class="control-label">
                        @lang('subject')
                    </label>
                    <input type="text" name="email_template_subject" id="email_template_subject"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors taggable"
                           value="{{ $this->mdl_email_templates->form_value('email_template_subject', true) " }}>
                </div>

                <div class="mb-4">
                    <label for="email_template_pdf_template" class="control-label">
                        @lang('pdf_template'):
                    </label>
                    <select name="email_template_pdf_template" id="email_template_pdf_template"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select">
                        <option value="">@lang('none')</option>

                        <optgroup label="@lang('invoices')">
                            @foreach($invoice_templates as $template)
                            <option class="hidden-invoice" value="{{ $template }}"
                                @php
                                    check_select($selected_pdf_template, $template)>
                                {{ $template }}
                            </option>@endforeach
                        </optgroup>

                        <optgroup label="@lang('quotes')">
                            @foreach($quote_templates as $template)
                            <option class="hidden-quote" value="{{ $template }}"
                                @php
                                    check_select($selected_pdf_template, $template)>
                                {{ $template }}
                            </option>@endforeach
                        </optgroup>
                    </select>
                </div>

                <hr>

                <div class="flex flex-wrap -mx-4">
                    <div class="w-full px-4 md:w-1/2">

                        <div class="mb-4">
                            <label for="email_template_body">@lang('body')</label>

                            <br>

                            <div class="html-tags inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm">
                                <span class="html-tag inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" data-tag-type="text-paragraph">
                                    <i class="fa fa-fw fa-paragraph"></i>
                                </span>
                                <span class="html-tag inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" data-tag-type="text-linebreak">
                                    &lt;br&gt;
                                </span>
                                <span class="html-tag inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" data-tag-type="text-bold">
                                    <i class="fa fa-fw fa-bold"></i>
                                </span>
                                <span class="html-tag inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" data-tag-type="text-italic">
                                    <i class="fa fa-fw fa-italic"></i>
                                </span>
                            </div>
                            <div class="html-tags inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm">
                                <span class="html-tag inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" data-tag-type="text-h1">H1</span>
                                <span class="html-tag inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" data-tag-type="text-h2">H2</span>
                                <span class="html-tag inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" data-tag-type="text-h3">H3</span>
                                <span class="html-tag inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" data-tag-type="text-h4">H4</span>
                            </div>
                            <div class="html-tags inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm">
                                <span class="html-tag inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" data-tag-type="text-code">
                                    <i class="fa fa-fw fa-code"></i>
                                </span>
                                <span class="html-tag inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" data-tag-type="text-hr">
                                    &lt;hr/&gt;
                                </span>
                                <span class="html-tag inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" data-tag-type="text-css">
                                    CSS
                                </span>
                            </div>

                            <textarea
                                name="email_template_body"
                                id="email_template_body"
                                rows="8"
                                class="email-template-body w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors taggable"
                            >{{ $this->mdl_email_templates->form_value('email_template_body', true) }}</textarea>

                            <br>

                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                    @lang('preview')
                                    <span id="email-template-preview-reload" class="float-right cursor-pointer">
                                        <i class="fa fa-refresh"></i>
                                    </span>
                                </div>
                                <div class="p-6">
                                    <iframe id="email-template-preview"></iframe>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="w-full px-4 md:w-1/2">

                        @include('email_templates.template-tags')

                    </div>
                </div>

            </div>
        </div>

    </div>

</form>

<script>
    $(function () {
        var email_template_type = "{{ $this->mdl_email_templates->form_value('email_template_type') }}";
        var $email_template_type_options = $("[name=email_template_type]");

        $email_template_type_options.click(function () {
            // remove class "show" and deselect any selected elements.
            $(".show").removeClass("show").parent("select").each(function () {
                this.options.selectedIndex = 0;
            });
            // add show class to corresponding class
            $(".hidden-" + $(this).val()).addClass("show");
        });
        @if(email_template_type === "") {
            $email_template_type_options.first().click();
        } else {
            $email_template_type_options.each(function () {
                @if($(this).val() === email_template_type) {
                    $(this).click();
                }
            });
        }
    });

    $(document).ready(function () {
        // find the type of template that has been loaded and enable/disable
        // the invoice and quote selects as required
        var inputValue = $('input[type="radio"]:checked').attr("value");

        @if(inputValue === 'quote') {
            $('#tags_invoice').prop('disabled', 'disabled');
            $('#tags_quote').prop('disabled', false);
        } else {
            // inputValue === 'invoice'
            $('#tags_invoice').prop('disabled', false);
            $('#tags_quote').prop('disabled', 'disabled');
        }

        // if the radio input for 'type of template' gets clicked, check the
        // new value and enable/disable the invoice and quote selects as required.
        $('input[type="radio"]').click(function () {
            var inputValue = $(this).attr("value");

            @if(inputValue === 'quote') {
                $('#tags_invoice').prop('disabled', 'disabled');
                $('#tags_quote').prop('disabled', false);
            } else {
                // inputValue === 'invoice'
                $('#tags_invoice').prop('disabled', false);
                $('#tags_quote').prop('disabled', 'disabled');
            }
        });
    });
</script>
