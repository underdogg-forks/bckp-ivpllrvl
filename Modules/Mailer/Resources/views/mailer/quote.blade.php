
<script>
    $(function () {
        var template_fields = ["body", "subject", "from_name", "from_email", "cc", "bcc", "pdf_template"];

        $('#email_template').change(function () {
            var email_template_id = $(this).val();

            @if(email_template_id === '') return;

            $.post("{{ url('email_templates/ajax/get_content');
?>", {
                email_template_id: email_template_id
            }, function (data) {
                inject_email_template(template_fields, json_parse(data, {{ (int) IP_DEBUG }}));
        });
    });

    var selected_email_template = {{ $email_template }};
    inject_email_template(template_fields, selected_email_template);
    })
    ;

    $(document).ready(function () {
        // this is the email quote window, disable the invoice select
        $('#tags_invoice').prop('disabled', 'disabled');
        $('#tags_quote').prop('disabled', false);
        // Fix blocked by browser if to_email field is empty
        $('#btn_cancel').on('click', function () {
            $('#to_email').prop('required', false);
        });
    });

</script>

<form method="post" action="{{ url('mailer/send_quote/' . $quote->quote_id) " }}>

    @csrf

    <div id="headerbar">
        <h1 class="headerbar-title">@lang('email_quote')</h1>

        <div class="headerbar-item float-right">
            <div class="inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm">
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors ajax-loader" name="btn_send" value="1">
                    <i class="fa fa-send"></i>
                    @lang('send')
                </button>
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" name="btn_cancel" id="btn_cancel" value="1">
                    <i class="fa fa-times"></i>
                    @lang('cancel')
                </button>
            </div>
        </div>
    </div>

    <div id="content">

        <div class="flex flex-wrap -mx-4">
            <div class="w-full px-4 col-md-8 col-md-offset-2">

                @include('layout.alerts')

                <div class="mb-4">
                    <label for="to_email">@lang('to_email')</label>
                    <input type="email" multiple name="to_email" id="to_email" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors" required
                           value="{{ $quote->client_email " }}>
                </div>

                <hr>

                <div class="mb-4">
                    <label for="email_template">@lang('email_template')</label>
                    <select name="email_template" id="email_template" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select">
                        <option value="">@lang('none')</option>
                        @foreach($email_templates as $email_template)
                        <option value="{{ $email_template->email_template_id }}"
                        @php
                            check_select($selected_email_template, $email_template->email_template_id) }}>
                                                    {!! $email_template->email_template_title !!}
                        </option>@endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="from_name">@lang('from_name')</label>
                    <input type="text" name="from_name" id="from_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                           value="{!! $quote->user_name !!}">
                </div>

                <div class="mb-4">
                    <label for="from_email">@lang('from_email')</label>
                    <input type="text" name="from_email" id="from_email" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors" required
                           value="{{ $quote->user_email " }}>
                </div>

                <div class="mb-4">
                    <label for="cc">@lang('cc')</label>
                    <input type="text" name="cc" id="cc" value="" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors">
                </div>

                <div class="mb-4">
                    <label for="bcc">@lang('bcc')</label>
                    <input type="text" name="bcc" id="bcc" value="" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors">
                </div>

                <div class="mb-4">
                    <label for="subject">@lang('subject')</label>
                    <input type="text" name="subject" id="subject" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                           value="@lang('quote') #{{ $quote->quote_number " }}>
                </div>

                <div class="mb-4">
                    <label for="pdf_template">@lang('pdf_template')</label>
                    <select name="pdf_template" id="pdf_template" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select">
                        <option value="">@lang('none')</option>
                        @foreach($pdf_templates as $pdf_template)
                        <option value="{{ $pdf_template }}"
                            @php
                                check_select($selected_pdf_template, $pdf_template)>
                            {{ $pdf_template }}
                        </option>@endforeach
                    </select>
                </div>

                <br>

                <div class="flex flex-wrap -mx-4">
                    <div class="w-full px-4 md:w-1/2">

                        <div class="mb-4">
                            <label for="body">@lang('body')</label>

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

                            <textarea name="body" id="body" rows="8"
                                      class="email-template-body w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors taggable"></textarea>

                            <br>

                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                    @lang('preview')
                                    <div id="email-template-preview-reload" class="float-right cursor-pointer">
                                        <i class="fa fa-refresh"></i>
                                    </div>
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

        <br>

        <div class="flex flex-wrap -mx-4">
            <div class="w-full px-4 col-md-8 col-md-offset-2">

                <div class="mb-4">
                    @php _dropzone_html(false)
                </div>

                <div class="mb-4">
                    <label for="quote-guest-url">@lang('guest_url')</label>
                    <div class="input-group">
                        <input type="text" id="quote-guest-url" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                               value="{{ url('guest/view/quote/' . $quote->quote_url_key) " }}>
                        <div class="input-group-addon to-clipboard cursor-pointer"
                             data-clipboard-target="#quote-guest-url">
                            <i class="fa fa-clipboard fa-fw"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</form>

    <?php
_dropzone_script($quote->quote_url_key, $quote->client_id);
