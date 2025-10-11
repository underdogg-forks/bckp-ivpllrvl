
<script>
    // Update check
    $(function () {
        // function to check if update excists for a version if atm on currend version
        function update(currend, checked) {
            // GetController the current version
            var curr = currend;
            // set the version to check to the passed variable
            var check = checked;
            @if(curr === check) {
                return 0;
            }
            var curr_components = curr.split(".");
            var check_components = check.split(".");

            var len = Math.min(curr_components.length, check_components.length);
            for (var i = 0; i < len; i++) {
                // curr bigger than check
                @if(parseInt(curr_components[i]) > parseInt(check_components[i])) {
                    return 0;
                }
                // check bigger than curr
                @if(parseInt(curr_components[i]) < parseInt(check_components[i])) {
                    return 1;
                }
            }
            @if(curr_components.length > check_components.length) {
                return 0;
            }
            @if(curr_components.length < check_components.length) {
                return 1;
            }
            return 0;
        }

        var checktime = 2000;
        // GetController the current version
        var ip_version = "{{ get_setting('current_version') }}";
        // GetController the latest version from the InvoicePlane IDS
        $.ajax({
            'url': 'https://ids.invoiceplane.com/updatecheck?cv=' + ip_version,
            'dataType': 'json',
            success: function (data) {
                @if(config('app.ip_debug'))
'console.log(data);'
@else
''
@endif
                    var
                updatecheck = data.current_version;
                // Compare each versions and replace the placeholder with a download button
                // or info label after 2 seconds
                setTimeout(function () {
                    @if(update(ip_version, updatecheck)) {
                        $('#updatecheck-updates-available').attr("href", "https://www.invoiceplane.com/downloads")
                        $('#updatecheck-loading').addClass('hidden');
                        $('#updatecheck-updates-available').removeClass('hidden');
                    } else {
                        $('#updatecheck-loading').addClass('hidden');
                        $('#updatecheck-no-updates').removeClass('hidden');
                    }
                }, checktime);
            },
            error: function (data) {
                $.ajax({
                    'url': 'https://ids.invoiceplane.org/updatecheck?cv=' + ip_version,
                    'dataType': 'json',
                    success: function (data) {
                        @if(config('app.ip_debug'))
'console.log(data);'
@else
''
@endif
                            var
                        updatecheck = data.current_version;
                        // Compare each versions and replace the placeholder with a download button
                        // or info label after 2 seconds
                        setTimeout(function () {
                            @if(update(ip_version, updatecheck)) {
                                $('#updatecheck-updates-available').attr("href", "https://www.invoiceplane.org/downloads")
                                $('#updatecheck-loading').addClass('hidden');
                                $('#updatecheck-updates-available').removeClass('hidden');
                            } else {
                                $('#updatecheck-loading').addClass('hidden');
                                $('#updatecheck-no-updates').removeClass('hidden');
                            }
                        }, checktime);
                    },
                    error: function (data) {
                        @if(config('app.ip_debug'))
'console.log(data);'
@else
''
@endif
                        $('#updatecheck-loading').addClass('hidden');
                        $('#updatecheck-failed').removeClass('hidden');
                    },
                });
            },
        });
        // GetController the latest news
        $.ajax({
            'url': 'https://ids.invoiceplane.com/get_news',
            'dataType': 'json',
            'success': function (data) {
                @if(config('app.ip_debug'))
'console.log(data);'
@else
''
@endif
                setTimeout(function () {
                    $('#ipnews-loading').addClass('hidden');
                    data.forEach(function (news) {
                        var ipnews = '<div class="alert alert-' + news.type '">';
                        ipnews += '<b>' + news.title + '</b><br/>';
                        ipnews += news.text + '<br/>';
                        @if(news.newsdate.date) ipnews += '<small>{{ trans('date') }}: ' + news.newsdate.date.substr(0, 11) + '</b><br/>';
                        ipnews += '</div>';
                        ipnews = ipnews.replace(/\n/g, "<br />");
                        $('#ipnews-container').append(ipnews);
                    });
                }, checktime);
            },
            'error': function (data) {
                $.ajax({
                    'url': 'https://ids.invoiceplane.org/get_news',
                    'dataType': 'json',
                    'success': function (data) {
                        @if(config('app.ip_debug'))
'console.log(data);'
@else
''
@endif
                        setTimeout(function () {
                            $('#ipnews-loading').addClass('hidden');
                            data.forEach(function (news) {
                                var ipnews = '<div class="alert alert-' + news.type '">';
                                ipnews += '<b>' + news.title + '</b><br/>';
                                ipnews += news.text + '<br/>';
                                @if(news.newsdate.date) ipnews += '<small>{{ trans('date') }}: ' + news.newsdate.date.substr(0, 11) + '</b><br/>';
                                ipnews += '</div>';
                                ipnews = ipnews.replace(/\n/g, "<br />");
                                $('#ipnews-container').append(ipnews);
                            });
                        }, checktime);
                    },
                    'error': function (data) {
                        @if(config('app.ip_debug'))
'console.log(data);'
@else
''
@endif
                        $('#ipnews-loading').addClass('hidden');
                        $('#ipnews-failed').removeClass('hidden');
                    },
                });
            },
        });
    });
</script>

<div class="w-full px-4 col-md-8 col-md-offset-2">

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
            @lang('updatecheck')
        </div>
        <div class="p-6">

            <div class="mb-4">
                <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors" value="{{ get_setting('current_version') }}"
                       readonly="readonly">
            </div>
            <div id="updatecheck-results">
                <div id="updatecheck-loading" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5 disabled">
                    <i class="fa fa-circle-o-notch fa-spin"></i> @lang('checking_for_updates')
                </div>

                <div id="updatecheck-no-updates" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5 disabled hidden">
                    @lang('no_updates_available')
                </div>

                <div id="updatecheck-failed" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors px-3 py-1.5 disabled hidden">
                    @lang('updatecheck_failed')
                </div>

                <a href="" id="updatecheck-updates-available" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors px-3 py-1.5 hidden" target="_blank">
                    @lang('updates_available')
                </a>
            </div>

        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
            @lang('invoiceplane_news')
        </div>
        <div class="p-6">

            <div id="ipnews-results">
                <div id="ipnews-loading" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5 disabled">
                    <i class="fa fa-circle-o-notch fa-spin"></i> @lang('checking_for_news')
                </div>

                <div id="ipnews-container"></div>
            </div>

        </div>
    </div>

</div>
