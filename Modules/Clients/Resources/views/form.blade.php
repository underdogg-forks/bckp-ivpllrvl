@php namespace Modules\Clients\Views;

$client_active = $this->mdl_clients->form_value('client_active');
$active = $client_active == 1 || !is_numeric($client_active) ? ' checked="checked"' : '';
$itsCompany = $this->mdl_clients->form_value('client_company') || $this->mdl_clients->form_value('client_vat_id');
if ($req_einvoicing) {
    // eInvoicing panel
    $nb_users = count($req_einvoicing->users);
    $me = $req_einvoicing->users[$_SESSION['user_id']]->show_table;
    $nb = $req_einvoicing->show_table;
    // Of users in error
    $ln = 'user' . (($nb ?: $nb_users) > 1 ? 's' : '');
    // tweak 1 on more nb_users no ok
    $user_toggle = ($req_einvoicing->show_table ? $me ? 'danger' : 'warning' : 'default') . ' ' . ($me ? '" aria-expanded="true' : '" collapsed" aria-expanded="false');
}
// eInvoicing enabled?
$einvoicingTip = $req_einvoicing ? ' data-toggle="tooltip" data-placement="bottom" title="e-' . trans('invoicing') . ' (' : '';
// tootip base
$einvoicingReq = $req_einvoicing ? $einvoicingTip . trans('required_field') . ')"' : '';
$einvoicingB2B = $req_einvoicing ? $einvoicingTip . 'B2B ' . trans('required_field') . ')"' : '';
$einvoicingOpt = $req_einvoicing ? $einvoicingTip . trans('optional') . ')"' : ''; @endphp
<script type="text/javascript">
    // eInvoicing button panel helper user(s) icon toggle
    const switch_fa_toggle = function (id) {
        const f = $('#'+id);f.toggleClass('fa-user').toggleClass('fa-users');
    }

    $(function () {
        $("#client_country").select2({
            placeholder: "@lang('country')",
            allowClear: true
        });

@include('clients.js.script_select_client_title.js')

    });

</script>

<form method="post">
    @csrf

    <div id="headerbar">
        <h1 class="headerbar-title">@lang('client_form')</h1>
        @include('layout.header_buttons')
    </div>
    <div id="content">

        @include('layout.alerts')

        <input class="hidden" name="is_update" type="hidden" value="{{ $this->mdl_clients->form_value('is_update') ? '1' : '0' }}">

        <div class="row">
            <div class="col-xs-12 col-sm-6"><!-- personal -->
                <div class="panel panel-default">
                    <div class="panel-heading form-inline clearfix">
                        @lang('personal_information')
                        <div class="pull-right">
                            <label for="client_active" class="control-label">
                                @lang('active_client')
                                <input id="client_active" name="client_active" type="checkbox" value="1"{{ $active }}>
                            </label>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="client_name">
                                @lang('client_name')
                            </label>
                            <input id="client_name" name="client_name" type="text" class="form-control"
                                   autofocus
                                   value="{{ $this->mdl_clients->form_value('client_name', true) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="client_surname">
                                @lang('client_surname_optional')
                            </label>
                            <input id="client_surname" name="client_surname" type="text" class="form-control"
                                   value="{{ $this->mdl_clients->form_value('client_surname', true) }}">
                        </div>
                        <div class="form-group"{{ $itsCompany ? $einvoicingB2B : $einvoicingOpt }}>
                            <label for="client_company">@lang('client_company') (@php _trans($itsCompany ? 'required_field' : 'optional'); @endphp)</label>

                            <div class="controls">
                                <input id="client_company" name="client_company" type="text" class="form-control"
                                       value="{{ $this->mdl_clients->form_value('client_company', true) }}">
                            </div>
                        </div>
                        <div class="form-group no-margin">
                            <label for="client_language">
                                @lang('lang')
                            </label>
                            <select name="client_language" id="client_language" class="form-control simple-select">
                                <option value="system">
                                    @lang('use_system_language')
                                </option>
@foreach($languages as $language) {
    $client_lang = $this->mdl_clients->form_value('client_language');
    @endphp
                                <option value="{{ $language }}"
                                    @php
    check_select($client_lang, $language);
    @endphp>
                                    {{ ucfirst($language) }}
                                </option>
@endif
                            </select>
                        </div>
                    </div>
                </div>

            </div>
@if($req_einvoicing)
            <div class="col-xs-12 col-sm-6"><!-- eInvoicing -->
                <div class="panel panel-default">

                    <div class="panel-heading">
                        e-@lang('invoicing')
                        <span class="{{ $xml_templates && $client_id ? 'pull-right' : 'hidden' }} toggle_einvoicing{{ $req_einvoicing->show_table ? ' btn btn-xs btn-default cursor-pointer alert-' . $user_toggle . '"
                              data-toggle="collapse" data-target=".einvoice-user-check-lists"
                              onclick="switch_fa_toggle(\'einvoice_users_check_fa_toggle\')' : '' }}">
                            <i class="fa fa-{{ $nb ? $me ? 'ban' : 'warning' : 'check-square-o text-success' }}"></i>
                            <span data-toggle="tooltip" data-placement="bottom" title="{{ '🗸 ' . ($nb_users - $nb) . '/' . $nb_users . ' ' . trans('user' . ($nb_users > 1 ? 's' : '')) }}">
                                {{ ($nb ?: $nb_users) . ' ' . trans($ln) }}
                            </span>
                            <i id="einvoice_users_check_fa_toggle" class="fa fa-{{ $nb ? 'user' . ($me ? '' : 's') : 'file-code-o' }} fa-margin"></i>
                        </span>
                    </div>

                    <div class="panel-body">
@if($xml_templates) {
        if ($client_id) {
            $this->layout->loadView('clients/partial_client_einvoicing');
        } else {
            @endphp
                        <div class="alert alert-warning small" style="font-size:medium;">
                            <i class="fa fa-exclamation-triangle fa-2x"></i>&nbsp;
                            @lang('einvoicing_no_enabled_hint')
                        </div>
@php
        }
        // End if client_id
    } else {
        @endphp
                        <div class="alert alert-info small" style="font-size:medium;">
                            <i class="fa fa-info"></i>&nbsp;
                            @lang('einvoicing_how_enable_hint')
                            <a href="https://github.com/InvoicePlane/InvoicePlane-e-invoices" target="_blank">InvoicePlane-e-invoices</a>
                        </div>
@php
    }
    // End if xml_templates
    @endphp
                    </div>
                </div>

            </div>
@php
}
// End if einvoicing @endphp

            <div class="col-xs-12 col-sm-6"><!-- Address -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        @lang('address')
                    </div>

                    <div class="panel-body">
                        <div class="form-group"{{ $einvoicingReq }}>
                            <label for="client_address_1">@lang('street_address')</label>

                            <div class="controls">
                                <input type="text" name="client_address_1" id="client_address_1" class="form-control"
                                       value="{{ $this->mdl_clients->form_value('client_address_1', true) }}">
                            </div>
                        </div>

                        <div class="form-group"{{ $einvoicingOpt }}>
                            <label for="client_address_2">@lang('street_address_2')</label>

                            <div class="controls">
                                <input type="text" name="client_address_2" id="client_address_2" class="form-control"
                                       value="{{ $this->mdl_clients->form_value('client_address_2', true) }}">
                            </div>
                        </div>

                        <div class="form-group"{{ $einvoicingReq }}>
                            <label for="client_city">@lang('city')</label>

                            <div class="controls">
                                <input type="text" name="client_city" id="client_city" class="form-control"
                                       value="{{ $this->mdl_clients->form_value('client_city', true) }}">
                            </div>
                        </div>

                        <div class="form-group"{{ $einvoicingOpt }}>
                            <label for="client_state">@lang('state')</label>

                            <div class="controls">
                                <input type="text" name="client_state" id="client_state" class="form-control"
                                       value="{{ $this->mdl_clients->form_value('client_state', true) }}">
                            </div>
                        </div>

                        <div class="form-group"{{ $einvoicingReq }}>
                            <label for="client_zip">@lang('zip_code')</label>

                            <div class="controls">
                                <input type="text" name="client_zip" id="client_zip" class="form-control"
                                       value="{{ $this->mdl_clients->form_value('client_zip', true) }}">
                            </div>
                        </div>

                        <div class="form-group"{{ $einvoicingReq }}>
                            <label for="client_country">@lang('country')</label>

                            <div class="controls">
                                <select name="client_country" id="client_country" class="form-control">
                                    <option value="">@lang('none')</option>
                                    @foreach($countries as $cldr => $country)
                                        <option value="{{ $cldr }}"
                                            @php
    check_select($selected_country, $cldr);
    @endphp
                                        >{{ $country }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
@foreach($custom_fields as $custom_field) {
    if ($custom_field->custom_field_location == 1) {
        print_field($this->mdl_clients, $custom_field, $custom_values);
    }
} @endphp
                    </div>

                </div>

            </div>

            <div class="col-xs-12 col-sm-6"><!-- Contact -->

                <div class="panel panel-default">

                    <div class="panel-heading">
                        @lang('contact_information')
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="client_invoicing_contact">@lang('contact') (@lang('invoicing'))</label>

                            <div class="controls">
                                <input type="text" name="client_invoicing_contact" id="client_invoicing_contact" class="form-control"
                                    value="{!! $this->mdl_clients->form_value('client_invoicing_contact') !!}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="client_phone">@lang('phone_number')</label>

                            <div class="controls">
                                <input type="text" name="client_phone" id="client_phone" class="form-control"
                                       value="{{ $this->mdl_clients->form_value('client_phone', true) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="client_fax">@lang('fax_number')</label>

                            <div class="controls">
                                <input type="text" name="client_fax" id="client_fax" class="form-control"
                                       value="{{ $this->mdl_clients->form_value('client_fax', true) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="client_mobile">@lang('mobile_number')</label>

                            <div class="controls">
                                <input type="text" name="client_mobile" id="client_mobile" class="form-control"
                                       value="{{ $this->mdl_clients->form_value('client_mobile', true) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="client_email">@lang('email_address')</label>

                            <div class="controls">
                                <input type="text" name="client_email" id="client_email" class="form-control"
                                       value="{{ $this->mdl_clients->form_value('client_email', true) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="client_web">@lang('web_address')</label>

                            <div class="controls">
                                <input type="text" name="client_web" id="client_web" class="form-control"
                                       value="{{ $this->mdl_clients->form_value('client_web', true) }}">
                            </div>
                        </div>

@foreach($custom_fields as $custom_field) {
    if ($custom_field->custom_field_location == 2) {
        print_field($this->mdl_clients, $custom_field, $custom_values);
    }
} @endphp
                    </div>

                </div>

            </div>

            <div class="col-xs-12 col-sm-6"><!-- Tax -->

                <div class="panel panel-default">
                    <div class="panel-heading">
                        @lang('tax_information')
                    </div>

                    <div class="panel-body">
                        <div class="form-group"{{ $itsCompany ? $einvoicingB2B : $einvoicingOpt }}>
                            <label for="client_vat_id">@lang('vat_id') (@php _trans($itsCompany ? 'required_field' : 'optional'); @endphp)</label>

                            <div class="controls">
                                <input type="text" name="client_vat_id" id="client_vat_id" class="form-control"
                                       value="{{ $this->mdl_clients->form_value('client_vat_id', true) }}">
                            </div>
                        </div>

                        <div class="form-group"{{ $einvoicingReq }}>
                            <label for="client_tax_code">@lang('tax_code')</label>

                            <div class="controls">
                                <input type="text" name="client_tax_code" id="client_tax_code" class="form-control"
                                       value="{{ $this->mdl_clients->form_value('client_tax_code', true) }}">
                            </div>
                        </div>

@foreach($custom_fields as $custom_field) {
    if ($custom_field->custom_field_location == 4) {
        print_field($this->mdl_clients, $custom_field, $custom_values);
    }
} @endphp
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-6"><!-- Personal -->
                <div class="panel panel-default">

                    <div class="panel-heading">
                        @lang('personal_information')
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="client_gender">@lang('gender')</label>
                            <div class="controls">
                                <select name="client_gender" id="client_gender"
                                        class="form-control simple-select" data-minimum-results-for-search="Infinity">
@php $genders = [trans('gender_male'), trans('gender_female'), trans('gender_other')];
$client_gender = $this->mdl_clients->form_value('client_gender');
foreach ($genders as $key => $val)
                                    <option value=" {{ $key }}" @php
    check_select($key, $client_gender);
    @endphp>
                                        {{ $val }}
                                    </option>
@endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
@php $client_title = $this->mdl_clients->form_value('client_title');
$is_custom_title = null === Modules\Core\Libraries\ClientTitleEnum::tryFrom($client_title); @endphp
                            <label for="client_title">@lang('client_title')</label>
                            <select name="client_title" id="client_title" class="form-control simple-select">
@foreach($client_title_choices as $client_title_choice)
                                <option
                                    value="{{ $client_title_choice }}"
                                    {{ $client_title === $client_title_choice ? 'selected' : '' }}
                                    {{ $is_custom_title && $client_title_choice === Modules\Core\Libraries\ClientTitleEnum::CUSTOM ? 'selected' : '' }}
                                >
                                    {{ ucfirst(trans($client_title_choice)) }}
                                </option>
@endif
                            </select>
                        </div>
                        <div class="form-group">
                            <input
                                id="client_title_custom"
                                name="client_title_custom"
                                type="text"
                                class="form-control{{ $is_custom_title ? '' : ' hidden' }}"
                                placeholder="{!! trans('custom_title') !!}"
                                value="{!! $client_title !!}"
                            >
                        </div>
                        <div class="form-group has-feedback">
                            <label for="client_birthdate">@lang('birthdate')</label>
@php $bdate = $this->mdl_clients->form_value('client_birthdate');
$bdate = $bdate && $bdate != '0000-00-00' ? date_from_mysql($bdate) : ''; @endphp
                            <div class="input-group">
                                <input type="text" name="client_birthdate" id="client_birthdate"
                                    class="form-control datepicker"
                                    value="{!! $bdate !!}">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar fa-fw"></i>
                                </span>
                            </div>
                        </div>

@if($this->mdl_settings->setting('sumex') == '1') {
    $avs = format_avs($this->mdl_clients->form_value('client_avs'));
    $insuredNumber = $this->mdl_clients->form_value('client_insurednumber');
    $veka = $this->mdl_clients->form_value('client_veka');
    @endphp

                        <div class="form-group">
                            <label for="client_avs">@lang('sumex_ssn')</label>
                            <div class="controls">
                                <input type="text" name="client_avs" id="client_avs" class="form-control"
                                       value="{!! $avs !!}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="client_insurednumber">@lang('sumex_insurednumber')</label>
                            <div class="controls">
                                <input type="text" name="client_insurednumber" id="client_insurednumber" class="form-control"
                                       value="@php
    _htmle($insuredNumber);
    @endphp">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="client_veka">@lang('sumex_veka')</label>
                            <div class="controls">
                                <input type="text" name="client_veka" id="client_veka" class="form-control"
                                       value="@php
    _htmle($veka);
    @endphp">
                            </div>
                        </div>

@php
}
// End if sumex @endphp

@php $default_custom = false;
foreach ($custom_fields as $custom_field) {
    if (!$default_custom && !$custom_field->custom_field_location) {
        $default_custom = true;
    }
    if ($custom_field->custom_field_location == 3) {
        print_field($this->mdl_clients, $custom_field, $custom_values);
    }
} @endphp
                    </div>

                </div>

            </div>
        </div>

@if($default_custom)
        <div class="row"><!-- Custom -->
            <div class="col-xs-12">

                <hr>

                <div class="panel panel-default">
                    <div class="panel-heading">@lang('custom_fields')</div>
                    <div class="panel-body">
                        <div class="row">
@php
    $classes = ['control-label', 'controls', '', 'form-group col-xs-12 col-sm-6'];
    foreach ($custom_fields as $custom_field) {
        if (!$custom_field->custom_field_location) {
            // == 0
            print_field($this->mdl_clients, $custom_field, $custom_values, $classes[0], $classes[1], $classes[2], $classes[3]);
        }
    }
    @endphp
                        </div>
                    </div>
                </div>

            </div>
        </div>
<?php
}
// End if custom_fields @endphp
    </div>
</form>
<?php
