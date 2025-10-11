
<script src="{{ _core_asset('js/zxcvbn.js') }}"></script>

<form method="post">

    @csrf

    <div id="headerbar">
        <h1 class="headerbar-title">@lang('change_password')</h1>
        {{ $this->layout->loadView('layout/header_buttons') }}
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">

                @include('layout.alerts')

                <div class="panel panel-default">
                    <div class="panel-heading">
                        @lang('change_password')
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="user_password">
                                @lang('password')
                            </label>
                            <input type="password" name="user_password" id="user_password"
                                   class="form-control passwordmeter-input" required>
                            <div class="progress" style="height:3px;">
                                <div class="progress-bar progress-bar-danger passmeter passmeter-1"
                                     style="width: 33%"></div>
                                <div class="progress-bar progress-bar-warning passmeter passmeter-2"
                                     style="display: none; width: 33%"></div>
                                <div class="progress-bar progress-bar-success passmeter passmeter-3"
                                     style="display: none; width: 34%"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="user_passwordv">
                                @lang('verify_password')
                            </label>
                            <input type="password" name="user_passwordv" id="user_passwordv"
                                   class="form-control" required>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

</form>
