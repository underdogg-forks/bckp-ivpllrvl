$client_active = $this->mdl_clients->form_value('client_active');
$active = $client_active == 1 || !is_numeric($client_active) ? ' checked="checked"' : '';
$itsCompany = $this->mdl_clients->form_value('client_company') || $this->mdl_clients->form_value('client_vat_id');
@if($req_einvoicing) {
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
$einvoicingOpt = $req_einvoicing ? $einvoicingTip . trans('optional') . ')"' : '';
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

        <input class="hidden" name="is_update" type="hidden" value="{{ $this->mdl_clients->form_value('is_update') ? '1' : '0' " }}>

        <div class="flex flex-wrap -mx-4">
            <div class="w-full px-4 col-sm-6"><!-- personal -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex flex-wrap gap-4 items-center clear-both">
                        @lang('personal_information')
                        <div class="float-right">
                            <label for="client_active" class="control-label">
                                @lang('active_client')
                                <input id="client_active" name="client_active" type="checkbox" value="1"{{ $active }}>
                            </label>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <label for="client_name">
                                @lang('client_name')
                            </label>
                            <input id="client_name" name="client_name" type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                   autofocus
                                   value="{{ $this->mdl_clients->form_value('client_name', true) }}" required>
                        </div>
                        <div class="mb-4">
                            <label for="client_surname">
                                @lang('client_surname_optional')
                            </label>
                            <input id="client_surname" name="client_surname" type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                   value="{{ $this->mdl_clients->form_value('client_surname', true) " }}>
                        </div>
                        <div class="mb-4"{{ $itsCompany ? $einvoicingB2B : $einvoicingOpt }}>
                            <label for="client_company">@lang('client_company') (@php _trans($itsCompany ? 'required_field' : 'optional'))</label>

                            <div class="controls">
                                <input id="client_company" name="client_company" type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ $this->mdl_clients->form_value('client_company', true) " }}>
                            </div>
                        </div>
                        <div class="mb-4 no-margin">
                            <label for="client_language">
                                @lang('lang')
                            </label>
                            <select name="client_language" id="client_language" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select">
                                <option value="system">
                                    @lang('use_system_language')
                                </option>
@foreach($languages as $language) {
    $client_lang = $this->mdl_clients->form_value('client_language')
                                <option value="{{ $language }}"
                                    @php
    check_select($client_lang, $language);
    >
                                    {{ ucfirst($language) }}
                                </option>@endforeach
                            </select>
                        </div>
                    </div>
                </div>

            </div>
@if($req_einvoicing)
            <div class="w-full px-4 col-sm-6"><!-- eInvoicing -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">

                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        e-@lang('invoicing')
                        <span class="{{ $xml_templates && $client_id ? ' float-right : 'hidden' }} toggle_einvoicing{{ $req_einvoicing->show_table inline-flex items-center gap-1 px-2 py-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-sm text-xs font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors cursor-pointer alert-' . $user_toggle"
                              data-toggle="collapse" data-target=".einvoice-user-check-lists"
                              onclick="switch_fa_toggle(\'einvoice_users_check_fa_toggle\')' : '' " }}>
                            <i class="fa fa-{{ $nb ? $me 'ban' : 'warning' 'check-square-o text-success'" }}></i>
                            <span data-toggle="tooltip" data-placement="bottom" title="{{ '🗸 ' . ($nb_users - $nb) . '/' . $nb_users . ' ' . trans('user' . ($nb_users > 1 ? 's' : '')) " }}>
                                {{ ($nb ?: $nb_users) . ' ' . trans($ln) }}
                            </span>
                            <i id="einvoice_users_check_fa_toggle" class="fa fa-{{ $nb ? 'user' . ($me '' : 's') 'file-code-o' }} fa-margin"></i>
                        </span>
                    </div>

                    <div class="p-6">
@if($xml_templates) {
        @if($client_id) {
            $this->layout->loadView('clients/partial_client_einvoicing');
        } else {

                        <div class="p-4 mb-4 text-yellow-700 dark:text-yellow-200 bg-yellow-100 dark:bg-yellow-900/50 border border-yellow-200 dark:border-yellow-800 rounded-lg small" style="font-size:medium;">
                            <i class="fa fa-exclamation-triangle fa-2x"></i>&nbsp;
                            @lang('einvoicing_no_enabled_hint')
                        </div>
@php
        }
        // End if client_id
    } else {

                        <div class="p-4 mb-4 text-cyan-700 dark:text-cyan-200 bg-cyan-100 dark:bg-cyan-900/50 border border-cyan-200 dark:border-cyan-800 rounded-lg small" style="font-size:medium;">
                            <i class="fa fa-info"></i>&nbsp;
                            @lang('einvoicing_how_enable_hint')
                            <a href="https://github.com/InvoicePlane/InvoicePlane-e-invoices" target="_blank">InvoicePlane-e-invoices</a>
                        </div>
@php
    }
    // End if xml_templates

                    </div>
                </div>

            </div>
@php
}
// End if einvoicing

            <div class="w-full px-4 col-sm-6"><!-- Address -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        @lang('address')
                    </div>

                    <div class="p-6">
                        <div class="mb-4"{{ $einvoicingReq }}>
                            <label for="client_address_1">@lang('street_address')</label>

                            <div class="controls">
                                <input type="text" name="client_address_1" id="client_address_1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ $this->mdl_clients->form_value('client_address_1', true) " }}>
                            </div>
                        </div>

                        <div class="mb-4"{{ $einvoicingOpt }}>
                            <label for="client_address_2">@lang('street_address_2')</label>

                            <div class="controls">
                                <input type="text" name="client_address_2" id="client_address_2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ $this->mdl_clients->form_value('client_address_2', true) " }}>
                            </div>
                        </div>

                        <div class="mb-4"{{ $einvoicingReq }}>
                            <label for="client_city">@lang('city')</label>

                            <div class="controls">
                                <input type="text" name="client_city" id="client_city" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ $this->mdl_clients->form_value('client_city', true) " }}>
                            </div>
                        </div>

                        <div class="mb-4"{{ $einvoicingOpt }}>
                            <label for="client_state">@lang('state')</label>

                            <div class="controls">
                                <input type="text" name="client_state" id="client_state" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ $this->mdl_clients->form_value('client_state', true) " }}>
                            </div>
                        </div>

                        <div class="mb-4"{{ $einvoicingReq }}>
                            <label for="client_zip">@lang('zip_code')</label>

                            <div class="controls">
                                <input type="text" name="client_zip" id="client_zip" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ $this->mdl_clients->form_value('client_zip', true) " }}>
                            </div>
                        </div>

                        <div class="mb-4"{{ $einvoicingReq }}>
                            <label for="client_country">@lang('country')</label>

                            <div class="controls">
                                <select name="client_country" id="client_country" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors">
                                    <option value="">@lang('none')</option>
                                    @foreach($countries as $cldr => $country)
                                        <option value="{{ $cldr }}"
                                            @php
    check_select($selected_country, $cldr)
                                        >{{ $country }}</option>@endforeach
                                </select>
                            </div>
                        </div>
@foreach($custom_fields as $custom_field) {
    @if($custom_field->custom_field_location == 1) {
        print_field($this->mdl_clients, $custom_field, $custom_values);
    }
}
                    </div>

                </div>

            </div>

            <div class="w-full px-4 col-sm-6"><!-- Contact -->

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">

                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        @lang('contact_information')
                    </div>

                    <div class="p-6">
                        <div class="mb-4">
                            <label for="client_invoicing_contact">@lang('contact') (@lang('invoicing'))</label>

                            <div class="controls">
                                <input type="text" name="client_invoicing_contact" id="client_invoicing_contact" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                    value="{!! $this->mdl_clients->form_value('client_invoicing_contact') !!}">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="client_phone">@lang('phone_number')</label>

                            <div class="controls">
                                <input type="text" name="client_phone" id="client_phone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ $this->mdl_clients->form_value('client_phone', true) " }}>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="client_fax">@lang('fax_number')</label>

                            <div class="controls">
                                <input type="text" name="client_fax" id="client_fax" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ $this->mdl_clients->form_value('client_fax', true) " }}>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="client_mobile">@lang('mobile_number')</label>

                            <div class="controls">
                                <input type="text" name="client_mobile" id="client_mobile" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ $this->mdl_clients->form_value('client_mobile', true) " }}>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="client_email">@lang('email_address')</label>

                            <div class="controls">
                                <input type="text" name="client_email" id="client_email" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ $this->mdl_clients->form_value('client_email', true) " }}>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="client_web">@lang('web_address')</label>

                            <div class="controls">
                                <input type="text" name="client_web" id="client_web" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ $this->mdl_clients->form_value('client_web', true) " }}>
                            </div>
                        </div>

@foreach($custom_fields as $custom_field) {
    @if($custom_field->custom_field_location == 2) {
        print_field($this->mdl_clients, $custom_field, $custom_values);
    }
}
                    </div>

                </div>

            </div>

            <div class="w-full px-4 col-sm-6"><!-- Tax -->

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        @lang('tax_information')
                    </div>

                    <div class="p-6">
                        <div class="mb-4"{{ $itsCompany ? $einvoicingB2B : $einvoicingOpt }}>
                            <label for="client_vat_id">@lang('vat_id') (@php _trans($itsCompany ? 'required_field' : 'optional'))</label>

                            <div class="controls">
                                <input type="text" name="client_vat_id" id="client_vat_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ $this->mdl_clients->form_value('client_vat_id', true) " }}>
                            </div>
                        </div>

                        <div class="mb-4"{{ $einvoicingReq }}>
                            <label for="client_tax_code">@lang('tax_code')</label>

                            <div class="controls">
                                <input type="text" name="client_tax_code" id="client_tax_code" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ $this->mdl_clients->form_value('client_tax_code', true) " }}>
                            </div>
                        </div>

@foreach($custom_fields as $custom_field) {
    @if($custom_field->custom_field_location == 4) {
        print_field($this->mdl_clients, $custom_field, $custom_values);
    }
}
                    </div>
                </div>
            </div>

            <div class="w-full px-4 col-sm-6"><!-- Personal -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">

                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        @lang('personal_information')
                    </div>

                    <div class="p-6">
                        <div class="mb-4">
                            <label for="client_gender">@lang('gender')</label>
                            <div class="controls">
                                <select name="client_gender" id="client_gender"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select" data-minimum-results-for-search="Infinity">
@php $genders = [trans('gender_male'), trans('gender_female'), trans('gender_other')];
$client_gender = $this->mdl_clients->form_value('client_gender');
@foreach($genders as $key => $val)
                                    <option value=" {{ $key }}" @php
    check_select($key, $client_gender)>
                                        {{ $val }}
                                    </option>@endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-4">
@php $client_title = $this->mdl_clients->form_value('client_title');
$is_custom_title = null === Modules\Core\Libraries\ClientTitleEnum::tryFrom($client_title);
                            <label for="client_title">@lang('client_title')</label>
                            <select name="client_title" id="client_title" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select">
@foreach($client_title_choices as $client_title_choice)
                                <option
                                    value="{{ $client_title_choice }}"
                                    {{ $client_title === $client_title_choice ? 'selected' : '' }}
                                    {{ $is_custom_title && $client_title_choice === Modules\Core\Libraries\ClientTitleEnum::CUSTOM ? 'selected' : '' }}
                                >
                                    {{ ucfirst(trans($client_title_choice)) }}
                                </option>@endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <input
                                id="client_title_custom"
                                name="client_title_custom"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors {{ $is_custom_title ? '' : ' hidden' }}"
                                placeholder="{!! trans('custom_title') !!}"
                                value="{!! $client_title !!}"
                            >
                        </div>
                        <div class="mb-4 has-feedback">
                            <label for="client_birthdate">@lang('birthdate')</label>
@php $bdate = $this->mdl_clients->form_value('client_birthdate');
$bdate = $bdate && $bdate != '0000-00-00' ? date_from_mysql($bdate) : '';
                            <div class="input-group">
                                <input type="text" name="client_birthdate" id="client_birthdate"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors datepicker"
                                    value="{!! $bdate !!}">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar fa-fw"></i>
                                </span>
                            </div>
                        </div>

@if($this->mdl_settings->setting('sumex') == '1') {
    $avs = format_avs($this->mdl_clients->form_value('client_avs'));
    $insuredNumber = $this->mdl_clients->form_value('client_insurednumber');
    $veka = $this->mdl_clients->form_value('client_veka')

                        <div class="mb-4">
                            <label for="client_avs">@lang('sumex_ssn')</label>
                            <div class="controls">
                                <input type="text" name="client_avs" id="client_avs" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{!! $avs !!}">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="client_insurednumber">@lang('sumex_insurednumber')</label>
                            <div class="controls">
                                <input type="text" name="client_insurednumber" id="client_insurednumber" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="@php
    _htmle($insuredNumber)">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="client_veka">@lang('sumex_veka')</label>
                            <div class="controls">
                                <input type="text" name="client_veka" id="client_veka" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="@php
    _htmle($veka)">
                            </div>
                        </div>

@php
}
// End if sumex

@php $default_custom = false;
@foreach($custom_fields as $custom_field) {
    @if(!$default_custom && !$custom_field->custom_field_location) {
        $default_custom = true;
    }
    @if($custom_field->custom_field_location == 3) {
        print_field($this->mdl_clients, $custom_field, $custom_values);
    }
}
                    </div>

                </div>

            </div>
        </div>

@if($default_custom)
        <div class="flex flex-wrap -mx-4"><!-- Custom -->
            <div class="w-full px-4">

                <hr>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">@lang('custom_fields')</div>
                    <div class="p-6">
                        <div class="flex flex-wrap -mx-4">
@php
    $classes = ['control-label', 'controls', '', 'form-group col-xs-12 col-sm-6'];
    @foreach($custom_fields as $custom_field) {
        @if(!$custom_field->custom_field_location) {
            // == 0
            print_field($this->mdl_clients, $custom_field, $custom_values, $classes[0], $classes[1], $classes[2], $classes[3]);
        }
    }

                        </div>
                    </div>
                </div>

            </div>
        </div>
<?php
}
// End if custom_fields
    </div>
</form>
