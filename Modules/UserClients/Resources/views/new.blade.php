@php namespace Modules\UserClients\Views; @endphp
<script>
    $(function () {
        $('#user_all_clients').click(function () {
            all_client_check();
        });

        function all_client_check() {
            if ($('#user_all_clients').is(':checked')) {
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

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">

                @include('layout.alerts')

                <input type="hidden" name="user_id" id="user_id"
                       value="{{ $user->user_id }}" required>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        {!! $user->user_name !!}
                    </div>
                    <div class="panel-body">

                        <div class="alert alert-info">
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
                            <select name="client_id" id="client_id" class="form-control simple-select"
                                    autofocus="autofocus" required>
                                @foreach($clients as $client) {
    echo '<option value="' . $client->client_id . '">' . htmlsc(format_client($client)) . '</option>';
} @endphp
                            </select>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>

</form>
