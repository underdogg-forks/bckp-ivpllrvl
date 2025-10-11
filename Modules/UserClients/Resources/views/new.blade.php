
<script>
    $(function () {
        $('#user_all_clients').click(function () {
            all_client_check();
        });

        function all_client_check() {
            @if($('#user_all_clients').is(':checked')) {
                $('#list_client').hide();
            } else {
                $('#list_client').show();
            }
        }

        all_client_check();
    });
</script>

<form method="post">

    @csrf

    <div id="headerbar">
        <h1 class="headerbar-title">@lang('assign_client')</h1>
        @include('layout.header_buttons')
    </div>

    <div id="content">

        <div class="flex flex-wrap -mx-4">
            <div class="w-full px-4 md:w-1/2 col-md-offset-3">

                @include('layout.alerts')

                <input type="hidden" name="user_id" id="user_id"
                       value="{{ $user->user_id }}" required>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        {!! $user->user_name !!}
                    </div>
                    <div class="p-6">

                        <div class="p-4 mb-4 text-cyan-700 dark:text-cyan-200 bg-cyan-100 dark:bg-cyan-900/50 border border-cyan-200 dark:border-cyan-800 rounded-lg">
                            <label>
                                <input type="checkbox" name="user_all_clients" id="user_all_clients"
                                       value="1" {{ $user->user_all_clients ? 'checked="checked"' : '' }}>
                                @lang('user_all_clients')
                            </label>

                            <div>
                                @lang('user_all_clients_text')
                            </div>
                        </div>

                        <div id="list_client">
                            <label for="client_id">@lang('client')</label>
                            <select name="client_id" id="client_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select"
                                    autofocus="autofocus" required>
                                @foreach($clients as $client) {
    echo '<option value="' . $client->client_id . '">' . htmlsc(format_client($client)) . '</option>';
}
                            </select>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>

</form>
