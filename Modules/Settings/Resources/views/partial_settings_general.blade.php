
<script>
    $(function () {
        $('#btn_generate_cron_key').click(function () {
            $.post("{{ url('settings/ajax/get_cron_key');
?>", function (data) {
                $('#cron_key').val(data);
            });
        });
    });
</script>

<div class="row">
    <div class="col-xs-12 col-md-8 col-md-offset-2">

        <div class="panel panel-default">
            <div class="panel-heading">
                @lang('general')
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[default_language]">
                                @lang('language')
                            </label>
                            <select name="settings[default_language]" id="settings[default_language]"
                                class="form-control simple-select">
                                @foreach($languages as $language) {
    $sys_lang = get_setting('default_language') }}
                <option value="
            {{ $language }}
            " @php
                check_select($sys_lang, $language)>
            {{ ucfirst($language) }}
            < /option>@endforeach
        </select>
        </div>
        </div>

            <div class="col-xs-12 col-md-6">
                <div class="form-group">
                    <label for="settings[system_theme]">
                        @lang('theme')
                    </label>
                    <select name="settings[system_theme]" id="settings[system_theme]"
                            class="form-control simple-select" data-minimum-results-for-search="Infinity">
                        @foreach($available_themes as $theme_key => $theme_name)
                        <option value="{{ $theme_key }}" @php
                            check_select(get_setting('system_theme'), $theme_key)>
                        {{ $theme_name }}
                        </option>@endforeach
                    </select>
                </div>
            </div>
        </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[first_day_of_week]">
                            @lang('first_day_of_week')
                        </label>
                        <select name="settings[first_day_of_week]" id="settings[first_day_of_week]"
                                class="form-control simple-select" data-minimum-results-for-search="Infinity">
                            @foreach($first_days_of_weeks as $first_day_of_week_id => $first_day_of_week_name)
                            <option value="{{ $first_day_of_week_id }}"
                                @php
                                    check_select(get_setting('first_day_of_week'), $first_day_of_week_id)>
                            {{ $first_day_of_week_name }}
                            </option>@endforeach
                        </select>
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[date_format]">
                            @lang('date_format')
                        </label>
                        <select name="settings[date_format]" id="settings[date_format]"
                                class="form-control simple-select">
                            @foreach($date_formats as $date_format)
                            <option value="{{ $date_format['setting'] }}"
                                @php
                                    check_select(get_setting('date_format'), $date_format['setting'])>
                            {{ $current_date->format($date_format['setting']) }}
                            ({{ $date_format['setting'] }})
                            </option>@endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[default_country]">
                            @lang('default_country')
                        </label>
                        <select name="settings[default_country]" id="settings[default_country]"
                                class="form-control simple-select">
                            <option value="">@lang('none')</option>
                            @foreach($countries as $cldr => $country)
                            <option value="{{ $cldr }}" @php
                                check_select(get_setting('default_country'), $cldr)>
                            {{ $country }}
                            </option>@endforeach
                        </select>
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="default_list_limit">
                            @lang('default_list_limit')
                        </label>
                        <input type="number" name="settings[default_list_limit]" id="default_list_limit"
                               class="form-control" minlength="1" min="1" required
                               value="{{ get_setting('default_list_limit', 15, true) }}">
                    </div>
                </div>
            </div>

        </div>
        </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    @lang('amount_settings')
                </div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="settings[currency_symbol]">
                                    @lang('currency_symbol')
                                </label>
                                <input type="text" name="settings[currency_symbol]" id="settings[currency_symbol]"
                                       class="form-control"
                                       value="{{ get_setting('currency_symbol', '', true) }}">
                            </div>
                        </div>

                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="settings[currency_symbol_placement]">
                                    @lang('currency_symbol_placement')
                                </label>
                                <select name="settings[currency_symbol_placement]"
                                        id="settings[currency_symbol_placement]"
                                        class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                    <option
                                        value="before" @php check_select(get_setting('currency_symbol_placement'), 'before')>
                                        @lang('before_amount')
                                    </option>
                                    <option
                                        value="after" @php check_select(get_setting('currency_symbol_placement'), 'after')>
                                        @lang('after_amount')
                                    </option>
                                    <option
                                        value="afterspace" @php check_select(get_setting('currency_symbol_placement'), 'afterspace')>
                                        @lang('after_amount_space')
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="settings[currency_code]">
                                    @lang('currency_code')
                                </label>
                                <select name="settings[currency_code]"
                                        id="settings[currency_code]"
                                        class="form-control simple-select">
                                    @foreach($gateway_currency_codes as $val => $key)
                                    <option value="{{ $val }}"
                                        @php
                                            check_select(get_setting('currency_code', '', true), $val)>
                                    {{ $val }}
                                    </option>@endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="tax_rate_decimal_places">
                                    @lang('tax_rate_decimal_places')
                                </label>
                                <select name="settings[tax_rate_decimal_places]" class="form-control simple-select"
                                        id="tax_rate_decimal_places" data-minimum-results-for-search="Infinity">
                                    <option
                                        value="2" @php check_select(get_setting('tax_rate_decimal_places'), '2')>
                                        2
                                    </option>
                                    <option
                                        value="3" @php check_select(get_setting('tax_rate_decimal_places'), '3'); >
                                        3
                                    </option>
                                </select>
                                <p class="help-block">@lang('tax_rate_decimal_places_hint')</p>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="settings[number_format]">
                                    @lang('number_format')
                                </label>
                                <select name="settings[number_format]" id="settings[number_format]"
                                        class="form-control simple-select"
                                        data-minimum-results-for-search="Infinity">
                                    @foreach($number_formats as $key => $value)
                                    <option value="{{ $key }}"
                                        @php
                                            check_select(get_setting('number_format'), $value['label'])>
                                    {{ _trans($value['label']) }}</option>@endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="settings[default_item_decimals]">
                                    @lang('default_item_decimals')
                                </label>
                                @php $current_default_item_decimals = get_setting('default_item_decimals')
                                <select name="settings[default_item_decimals]" id="settings[default_item_decimals]"
                                        class="form-control simple-select"
                                        data-minimum-results-for-search="Infinity">
                                <option value="1" @php check_select($current_default_item_decimals, '1'); >1
                                </option>
                                <option value="2" @php check_select($current_default_item_decimals, '2'); >2
                                </option>
                                <option value="3" @php check_select($current_default_item_decimals, '3'); >3
                                </option>
                                <option value="4" @php check_select($current_default_item_decimals, '4'); >4
                                </option>
                                <option value="5" @php check_select($current_default_item_decimals, '5'); >5
                                </option>
                                <option value="6" @php check_select($current_default_item_decimals, '6'); >6
                                </option>
                                <option value="7" @php check_select($current_default_item_decimals, '7'); >7
                                </option>
                                <option value="8" @php check_select($current_default_item_decimals, '8'); >8
                                </option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    @lang('dashboard')
                </div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="settings[quote_overview_period]">
                                    @lang('quote_overview_period')
                                </label>
                                <select name="settings[quote_overview_period]" id="settings[quote_overview_period]"
                                        class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                    <option
                                        value="this-month" @php check_select(get_setting('quote_overview_period'), 'this-month')>
                                        @lang('this_month')
                                    </option>
                                    <option
                                        value="last-month" @php check_select(get_setting('quote_overview_period'), 'last-month')>
                                        @lang('last_month')
                                    </option>
                                    <option
                                        value="this-quarter" @php check_select(get_setting('quote_overview_period'), 'this-quarter')>
                                        @lang('this_quarter')
                                    </option>
                                    <option
                                        value="last-quarter" @php check_select(get_setting('quote_overview_period'), 'last-quarter')>
                                        @lang('last_quarter')
                                    </option>
                                    <option
                                        value="this-year" @php check_select(get_setting('quote_overview_period'), 'this-year')>
                                        @lang('this_year')
                                    </option>
                                    <option
                                        value="last-year" @php check_select(get_setting('quote_overview_period'), 'last-year')>
                                        @lang('last_year')
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="settings[invoice_overview_period]">
                                    @lang('invoice_overview_period')
                                </label>
                                <select name="settings[invoice_overview_period]" id="settings[invoice_overview_period]"
                                        class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                    <option
                                        value="this-month" @php check_select(get_setting('invoice_overview_period'), 'this-month')>
                                        @lang('this_month')
                                    </option>
                                    <option
                                        value="last-month" @php check_select(get_setting('invoice_overview_period'), 'last-month')>
                                        @lang('last_month')
                                    </option>
                                    <option
                                        value="this-quarter" @php check_select(get_setting('invoice_overview_period'), 'this-quarter')>
                                        @lang('this_quarter')
                                    </option>
                                    <option
                                        value="last-quarter" @php check_select(get_setting('invoice_overview_period'), 'last-quarter')>
                                        @lang('last_quarter')
                                    </option>
                                    <option
                                        value="this-year" @php check_select(get_setting('invoice_overview_period'), 'this-year')>
                                        @lang('this_year')
                                    </option>
                                    <option
                                        value="last-year" @php check_select(get_setting('invoice_overview_period'), 'last-year')>
                                        @lang('last_year')
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="disable_quickactions">
                                    @lang('disable_quickactions')
                                </label>
                                <select name="settings[disable_quickactions]" class="form-control simple-select"
                                        id="disable_quickactions" data-minimum-results-for-search="Infinity">
                                    <option value="0">
                                        @lang('no')
                                    </option>
                                    <option
                                        value="1" @php check_select(get_setting('disable_quickactions'), '1')>
                                        @lang('yes')
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    @lang('interface')
                </div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="disable_sidebar">
                                    @lang('disable_sidebar')
                                </label>
                                <select name="settings[disable_sidebar]" class="form-control simple-select"
                                        id="disable_sidebar" data-minimum-results-for-search="Infinity">
                                    <option value="0">
                                        @lang('no')
                                    </option>
                                    <option value="1" @php check_select(get_setting('disable_sidebar'), '1')>
                                        @lang('yes')
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="settings[custom_title]">
                                    @lang('custom_title')
                                </label>
                                <input type="text" name="settings[custom_title]" id="settings[custom_title]"
                                       class="form-control"
                                       value="{{ get_setting('custom_title', '', true) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="monospace_amounts">
                                    @lang('monospaced_font_for_amounts')
                                </label>
                                <select name="settings[monospace_amounts]" class="form-control simple-select"
                                        id="monospace_amounts" data-minimum-results-for-search="Infinity">
                                    <option value="0">@lang('no')</option>
                                    <option value="1" @php check_select(get_setting('monospace_amounts'), '1')>
                                        @lang('yes')
                                    </option>
                                </select>

                                <p class="help-block">
                                    @lang('example'):
                                    <span style="font-family: Monaco, Lucida Console, monospace">
                                    {{ format_currency(123456.78) }}
                                </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="login_logo">
                                    @lang('login_logo')
                                </label>
                                @if(get_setting('login_logo'))
                                <br/>
                                <img class="personal_logo"
                                     src="{{ url() }}uploads/{{ get_setting('login_logo') }}"><br>
                                    {{ anchor('settings/remove_logo/login', trans('remove_logo')) }}<br/>@endforeach
                                    <input type="file" name="login_logo" id="login_logo" class="form-control"/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="settings[reports_in_new_tab]">
                                    @lang('open_reports_in_new_tab')
                                </label>
                                <select name="settings[reports_in_new_tab]" id="settings[reports_in_new_tab]"
                                        class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                    <option value="0">@lang('no')</option>
                                    <option
                                        value="1" @php check_select(get_setting('reports_in_new_tab'), '1')>
                                        @lang('yes')
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="settings[show_responsive_itemlist]">
                                    @lang('show_responsive_itemlist')
                                </label>
                                <select name="settings[show_responsive_itemlist]"
                                        id="settings[show_responsive_itemlist]"
                                        class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                    <option value="0">
                                        @lang('no')
                                    </option>
                                    <option
                                        value="1" @php check_select(get_setting('show_responsive_itemlist'), '1')>
                                        @lang('yes')
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    @lang('system_settings')
                </div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-xs-12 col-md-6">

                            <div class="form-group">
                                <label for="settings[bcc_mails_to_admin]">
                                    @lang('bcc_mails_to_admin')
                                </label>
                                <select name="settings[bcc_mails_to_admin]" id="settings[bcc_mails_to_admin]"
                                        class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                    <option value="0">@lang('no')</option>
                                    <option
                                        value="1" @php check_select(get_setting('bcc_mails_to_admin'), '1')>
                                        @lang('yes')
                                    </option>
                                </select>

                                <p class="help-block">@lang('bcc_mails_to_admin_hint')</p>
                            </div>

                        </div>
                        <div class="col-xs-12 col-md-6">

                            <div class="form-group">
                                <label for="cron_key">
                                    @lang('cron_key')
                                </label>
                                <div class="input-group">
                                    <input type="text" name="settings[cron_key]" id="cron_key" class="form-control"
                                           readonly
                                           value="{{ get_setting('cron_key') }}">
                                        <div class="input-group-btn">
                                            <button id="btn_generate_cron_key" type="button"
                                                    class="btn btn-primary btn-block">
                                                <i class="fa fa-recycle fa-margin"></i> @lang('generate')
                                            </button>
                                        </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
        </div>
