
<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post" action="{{ url($this->uri->uri_string());
?>">

            @csrf

            <legend>@lang('setup_database_details')</legend>

            @if(!$database['success'])
                @if($database['message'] && $_POST)
                    <div class="alert alert-danger">
                        <b>@php
        @lang('failure') }}</b><br>
                        {{ $database['message'] }}
                    </div>
                @endif

                <p>@lang('setup_database_message')</p>

                <div class=" form-group
        ">
        <label for="db_hostname">
            @lang('hostname')
        </label>
        <input type="text" name="db_hostname" id="db_hostname" class="form-control"
               value="{{ $this->input->post('db_hostname') ? $this->input->post('db_hostname') : 'localhost' }}">
        <span class="help-block">@lang('setup_db_hostname_info')</span>
    </div>

    <div class="form-group">
        <label for="db_port">
            @lang('port')
        </label>
        <input type="text" name="db_port" id="db_port" class="form-control"
               value="{{ $this->input->post('db_port') ? $this->input->post('db_port') : 3306 }}">
        <span class="help-block">@lang('setup_db_port_info')</span>
    </div>

    <div class="form-group">
        <label>
            @lang('username')
        </label>
        <input type="text" name="db_username" id="db_username" class="form-control"
               value="{{ $this->input->post('db_username') }}">
        <span class="help-block">@lang('setup_db_username_info')</span>
    </div>

    <div class="form-group">
        <label>
            @lang('password')
        </label>
        <input type="password" name="db_password" id="db_password" class="form-control"
               value="{{ $this->input->post('db_password') }}">
        <span class="help-block">@lang('setup_db_password_info')</span>
    </div>

    <div class="form-group">
        <label>
            @lang('database')
        </label>
        <input type="text" name="db_database" id="db_database" class="form-control"
               value="{{ $this->input->post('db_database') }}">
        <span class="help-block">@lang('setup_db_database_info')</span>
    </div>
    @endif

    @if($errors)
    <input type="submit" class="btn btn-primary" name="btn_try_again"
           value="@lang('try_again')">
    @else
    <p><i class="fa fa-check text-success fa-margin"></i>
        @lang('setup_database_configured_message')
    </p>
    <input type="submit" class="btn btn-success" name="btn_continue"
           value="@lang('continue')">
        @endif

    </form>

</div>
</div>
