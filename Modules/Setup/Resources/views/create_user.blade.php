@php namespace Modules\Setup\Views; @endphp
<script>
    $(function () {
        $("[name='user_country']").select2({
            placeholder: "@lang('country')",
            allowClear: true
        });

        var password_input = $('.passwordmeter-input');
        if (password_input) {
            password_input.on('input', function () {
                var strength = zxcvbn(password_input.val());

                $('.passmeter-2, .passmeter-3').hide();
                if (strength.score === 4) {
                    $('.passmeter-2, .passmeter-3').show();
                } else if (strength.score === 3) {
                    $('.passmeter-2').show();
                }
            });
        }
    });
</script>

<script src="@php _core_asset('js/zxcvbn.js'); @endphp"></script>

<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post" action="{{ url($this->uri->uri_string()) }}">

            @csrf

            <input type="hidden" name="user_type" value="1">

            <legend>@lang('setup_create_user')</legend>

            {{ $this->layout->loadView('layout/alerts') }}

            <p>@lang('setup_create_user_message')</p>

            <div class="form-group">
                <label for="user_email">
                    @lang('email_address')
                </label>
                <input type="email" name="user_email" id="user_email" class="form-control"
                       value="{{ $this->mdl_users->form_value('user_email', true) }}">
                <span class="help-block">@lang('setup_user_email_info')</span>
            </div>

            <div class="form-group">
                <label for="user_name">
                    @lang('name')
                </label>
                <input type="text" name="user_name" id="user_name" class="form-control"
                       value="{{ $this->mdl_users->form_value('user_name', true) }}">
                <span class="help-block">@lang('setup_user_name_info')</span>
            </div>

            <div class="form-group">
                <label for="user_password">
                    @lang('password')
                </label>
                <input type="password" name="user_password" id="user_password"
                       class="form-control passwordmeter-input">
                <div class="progress" style="height:3px;">
                    <div class="progress-bar progress-bar-danger passmeter passmeter-1" style="width: 33%"></div>
                    <div class="progress-bar progress-bar-warning passmeter passmeter-2"
                         style="display: none; width: 33%"></div>
                    <div class="progress-bar progress-bar-success passmeter passmeter-3"
                         style="display: none; width: 34%"></div>
                </div>

                <span class="help-block">@lang('setup_user_password_info')</span>
            </div>

            <div class="form-group">
                <label for="user_passwordv">
                    @lang('verify_password')
                </label>
                <input type="password" name="user_passwordv" id="user_passwordv" class="form-control">
                <span class="help-block">@lang('setup_user_password_verify_info')</span>
            </div>

            <div class="form-group">
                <label for="user_language">
                    @lang('lang')
                </label>
                <select name="user_language" id="user_language" class="form-control simple-select">
                    <option value="system">
                        {{ trans('use_system_language') }}
                    </option>
                    @foreach($languages as $language)
                    <option value="{{ $language }}">
                        {{ ucfirst($language) }}
                    </option>
                    @endif
                </select>
            </div>

            <legend>@lang('address')</legend>
            <p>@lang('setup_user_address_info')</p>

            <div class="form-group">
                <label>
                    @lang('street_address')
                </label>
                <input type="text" name="user_address_1" id="user_address_1" class="form-control"
                       value="{{ $this->mdl_users->form_value('user_address_1', true) }}">
            </div>

            <div class="form-group">
                <label>
                    @lang('street_address_2')
                </label>
                <input type="text" name="user_address_2" id="user_address_2" class="form-control"
                       value="{{ $this->mdl_users->form_value('user_address_2', true) }}"
                       placeholder="@lang('optional')">
            </div>

            <div class="form-group">
                <label>
                    @lang('city')
                </label>
                <input type="text" name="user_city" id="user_city" class="form-control"
                       value="{{ $this->mdl_users->form_value('user_city', true) }}"
                       placeholder="@lang('optional')">
            </div>

            <div class="form-group">
                <label>
                    @lang('state')
                </label>
                <input type="text" name="user_state" id="user_state" class="form-control"
                       value="{{ $this->mdl_users->form_value('user_state', true) }}"
                       placeholder="@lang('optional')">
            </div>

            <div class="form-group">
                <label>
                    @lang('zip_code')
                </label>
                <input type="text" name="user_zip" id="user_zip" class="form-control"
                       value="{{ $this->mdl_users->form_value('user_zip', true) }}"
                       placeholder="@lang('optional')">
            </div>

            <div class="form-group">
                <label>
                    @lang('country')
                </label>
                <select name="user_country" class="form-control simple-select">
                    <option value="">@lang('none')</option>
                    @foreach($countries as $cldr => $country)
                    <option value="{{ $cldr }}"
                        @php
                            check_select($this->mdl_users->form_value('user_country'), $cldr);
                        @endphp>
                        {{ $country }}
                    </option>
                        <?php
                    } @endphp
                </select>
            </div>

            <legend>@lang('setup_other_contact')</legend>

            <p>@lang('setup_user_contact_info')</p>

            <div class="form-group">
                <label>
                    @lang('phone')
                </label>
                <input type="text" name="user_phone" id="user_phone" class="form-control"
                       value="{{ $this->mdl_users->form_value('user_phone', true) }}"
                       placeholder="@lang('optional')">
            </div>

            <div class="form-group">
                <label>
                    @lang('fax')
                </label>
                <input type="text" name="user_fax" id="user_fax" class="form-control"
                       value="{{ $this->mdl_users->form_value('user_fax', true) }}"
                       placeholder="@lang('optional')">
            </div>

            <div class="form-group">
                <label>
                    @lang('mobile')
                </label>
                <input type="text" name="user_mobile" id="user_mobile" class="form-control"
                       value="{{ $this->mdl_users->form_value('user_mobile', true) }}"
                       placeholder="@lang('optional')">
            </div>

            <div class="form-group">
                <label>
                    @lang('web')
                </label>
                <input type="text" name="user_web" id="user_web" class="form-control"
                       value="{{ $this->mdl_users->form_value('user_web', true) }}"
                       placeholder="@lang('optional')">
            </div>

            <input type="submit" class="btn btn-success" name="btn_continue"
                   value="@lang('continue')">

        </form>

    </div>
</div>
<?php
