@php namespace Modules\Settings\Views; @endphp
<script>
    $(function () {
        var online_payment_select = $('#online-payment-select');
        online_payment_select.select2().on('change', function () {
            var driver = online_payment_select.val();
            $('.gateway-settings:not(.active-gateway)').addClass('hidden');
            $('#gateway-settings-' + driver).removeClass('hidden').addClass('active-gateway');
        });
    });
</script>

<div class="row">
    <div class="col-xs-12 col-md-8 col-md-offset-2">

        <div class="panel panel-default">
            <div class="panel-heading">
                @@lang('online_payments')
            </div>
            <div class="panel-body">

                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="hidden" name="settings[enable_online_payments]" value="0">
                            <input type="checkbox" name="settings[enable_online_payments]" value="1"
                                @php check_select(get_setting('enable_online_payments'), 1, '==', true); @endphp>
                            @@lang('enable_online_payments')
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="online-payment-select">
                        @@lang('add_payment_provider')
                    </label>
                    <select id="online-payment-select" class="form-control">
                        <option value="">@@lang('none')</option>
                        @php foreach ($gateway_drivers as $driver => $fields) {
    $d = mb_strtolower($driver);
    @endphp
                            <option value="{{ $d }}">
                                {{ ucwords(str_replace('_', ' ', $driver)) }}
                            </option>
                        @php
} @endphp
                    </select>
                </div>

            </div>
        </div>

        @php foreach ($gateway_drivers as $driver => $fields) {
    $d = mb_strtolower($driver);
    @endphp
            <div id="gateway-settings-{{ $d }}"
                class="gateway-settings panel panel-default {{ get_setting('gateway_' . $d . '_enabled') ? 'active-gateway' : 'hidden' }}">

                <div class="panel-heading">
                    {{ ucwords(str_replace('_', ' ', $driver)) }}
                    <div class="pull-right">
                        <div class="checkbox no-margin">
                            <label>
                                <input type="hidden" name="settings[gateway_{{ $d }}_enabled]" value="0">
                                <input type="checkbox" name="settings[gateway_{{ $d }}_enabled]" value="1"
                                    id="settings[gateway_{{ $d }}_enabled]"
                                    @php
    check_select(get_setting('gateway_' . $d . '_enabled'), 1, '==', true);
    @endphp>
                                @@lang('enabled')
                            </label>
                        </div>
                    </div>
                </div>

                <div class="panel-body small">

                    @php
    foreach ($fields as $key => $setting) {
        @endphp
                        @php
        if ($setting['type'] == 'checkbox') {
            @endphp
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="settings[gateway_{{ $d }}_{{ $key }}]"
                                        value="0">
                                    <input type="checkbox" name="settings[gateway_{{ $d }}_{{ $key }}]"
                                        value="1"
                                        @php
            check_select(get_setting('gateway_' . $d . '_' . $key), 1, '==', true);
            @endphp>
                                    @php
            _trans('online_payment_' . $key, '', $setting['label']);
            @endphp
                                </label>
                            </div>

                        @php
        } else {
            @endphp
                            <div class="form-group">
                                <label for="settings[gateway_{{ $d }}_{{ $key }}]">
                                    @php
            _trans('online_payment_' . $key, '', $setting['label']);
            @endphp
                                </label>
                                <input type="{{ $setting['type'] }}" class="form-control"
                                    name="settings[gateway_{{ $d }}_{{ $key }}]"
                                    id="settings[gateway_{{ $d }}_{{ $key }}]"
                                    @php
            if ($setting['type'] == 'password') {
                @endphp
                                        value="{{ $this->crypt->decode(get_setting('gateway_' . $d . '_' . $key)) }}"
                                    @php
            } else {
                @endphp
                                        value="{{ get_setting('gateway_' . $d . '_' . $key) }}"
                                    @php
            }
            @endphp
                                >
                                @php
            if ($setting['type'] == 'password') {
                @endphp
                                    <input type="hidden" value="1"
                                        name="settings[gateway_{{ $d . '_' . $key }}_field_is_password]">
                                @php
            }
            @endphp
                            </div>

                        @php
        }
        @endphp
                    @php
    }
    @endphp

                    <hr>

                    <div class="form-group">
                        <label for="settings[gateway_{{ $d }}_currency]">
                            @@lang('currency')
                        </label>
                        <select name="settings[gateway_{{ $d }}_currency]"
                            id="settings[gateway_{{ $d }}_currency]"
                            class="form-control simple-select">
                            @php
    foreach ($gateway_currency_codes as $val => $key) {
        @endphp
                                <option value="{{ $val }}"
                                    @php
        check_select(get_setting('gateway_' . $d . '_currency') ?: get_setting('currency_code'), $val);
        @endphp>
                                    {{ $val }}
                                </option>
                            @php
    }
    @endphp
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="settings[gateway_{{ $d }}_payment_method]">
                            @@lang('online_payment_method')
                        </label>
                        <select name="settings[gateway_{{ $d }}_payment_method]"
                            id="settings[gateway_{{ $d }}_payment_method]"
                            class="form-control simple-select">
                            <option value="">@@lang('none')</option>
                            @php
    foreach ($payment_methods as $payment_method) {
        @endphp
                                <option value="{{ $payment_method->payment_method_id }}"
                                    @php
        check_select(get_setting('gateway_' . $d . '_payment_method'), $payment_method->payment_method_id);
        @endphp>
                                    {{ $payment_method->payment_method_name }}
                                </option>
                            @php
    }
    @endphp
                        </select>
                    </div>

                </div>

            </div>
        <?php
} @endphp

    </div>
</div>
<?php 
