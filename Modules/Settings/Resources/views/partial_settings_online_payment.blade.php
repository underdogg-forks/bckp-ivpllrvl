
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

<div class="flex flex-wrap -mx-4">
    <div class="w-full px-4 col-md-8 col-md-offset-2">

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                @lang('online_payments')
            </div>
            <div class="p-6">

                <div class="mb-4">
                    <div class="checkbox">
                        <label>
                            <input type="hidden" name="settings[enable_online_payments]" value="0">
                            <input type="checkbox" name="settings[enable_online_payments]" value="1"
                                @php check_select(get_setting('enable_online_payments'), 1, '==', true)>
                            @lang('enable_online_payments')
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="online-payment-select">
                        @lang('add_payment_provider')
                    </label>
                    <select id="online-payment-select" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors">
                        <option value="">@lang('none')</option>
                        @foreach($gateway_drivers as $driver => $fields) {
    $d = mb_strtolower($driver)
                        <option value="{{ $d " }}>
                            {{ ucwords(str_replace('_', ' ', $driver)) }}
                        </option>@endforeach
                    </select>
                </div>

            </div>
        </div>

        @foreach($gateway_drivers as $driver => $fields) {
    $d = mb_strtolower($driver);

        <div id="gateway-settings-{{ $d }}"
             class="gateway-settings bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm {{ get_setting('gateway_' . $d '_enabled') ? 'active-gateway' : 'hidden'" }}>

            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                {{ ucwords(str_replace('_', ' ', $driver)) }}
                <div class="float-right">
                    <div class="checkbox no-margin">
                        <label>
                            <input type="hidden" name="settings[gateway_{{ $d }}_enabled]" value="0">
                            <input type="checkbox" name="settings[gateway_{{ $d }}_enabled]" value="1"
                                   id="settings[gateway_{{ $d }}_enabled]"
                                @php
                                    check_select(get_setting('gateway_' . $d . '_enabled'), 1, '==', true);
                                >
                            @lang('enabled')
                        </label>
                    </div>
                </div>
            </div>

            <div class="p-6 small">

                @foreach($fields as $key => $setting)
                @if($setting['type'] == 'checkbox')
                <div class="checkbox">
                    <label>
                        <input type="hidden" name="settings[gateway_{{ $d }}_{{ $key }}]"
                               value="0">
                        <input type="checkbox" name="settings[gateway_{{ $d }}_{{ $key }}]"
                               value="1"
                            @php
                                check_select(get_setting('gateway_' . $d . '_' . $key), 1, '==', true)>
                        {{ _trans('online_payment_' . $key, '', $setting['label']) }}</label>
                </div>

                @else
                <div class="mb-4">
                    <label for="settings[gateway_{{ $d }}_{{ $key }}]">
                        {{ _trans('online_payment_' . $key, '', $setting['label']) }}</label>
                    <input type="{{ $setting['type'] }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                           name="settings[gateway_{{ $d }}_{{ $key }}]"
                           id="settings[gateway_{{ $d }}_{{ $key }}]"
                           @if($setting['type'] == 'password')
                           value="{{ $this->crypt->decode(get_setting('gateway_' . $d . '_' . $key)) }}"
                           @else
                           value="{{ get_setting('gateway_' . $d . '_' . $key) }}"@endforeach
                    >
                    @if($setting['type'] == 'password')
                    <input type="hidden" value="1"
                           name="settings[gateway_{{ $d . '_' . $key }}_field_is_password]">@endforeach
                </div>@endforeach@endforeach

                <hr>

                <div class="mb-4">
                    <label for="settings[gateway_{{ $d }}_currency]">
                        @lang('currency')
                    </label>
                    <select name="settings[gateway_{{ $d }}_currency]"
                            id="settings[gateway_{{ $d }}_currency]"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select">
                        @foreach($gateway_currency_codes as $val => $key)
                        <option value="{{ $val }}"
                            @php
                                check_select(get_setting('gateway_' . $d . '_currency') ?: get_setting('currency_code'), $val)>
                            {{ $val }}
                        </option>@endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="settings[gateway_{{ $d }}_payment_method]">
                        @lang('online_payment_method')
                    </label>
                    <select name="settings[gateway_{{ $d }}_payment_method]"
                            id="settings[gateway_{{ $d }}_payment_method]"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select">
                        <option value="">@lang('none')</option>
                        @foreach($payment_methods as $payment_method)
                        <option value="{{ $payment_method->payment_method_id }}"
                            @php
                                check_select(get_setting('gateway_' . $d . '_payment_method'), $payment_method->payment_method_id)>
                            {{ $payment_method->payment_method_name }}
                        </option>@endforeach
                    </select>
                </div>

            </div>

        </div>@endforeach

    </div>
</div>
