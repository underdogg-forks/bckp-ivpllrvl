@php
    $client_active = $this->mdl_clients->form_value('client_active');
    $active = $client_active == 1 || !is_numeric($client_active) ? ' checked="checked"' : '';
    $itsCompany = $this->mdl_clients->form_value('client_company') || $this->mdl_clients->form_value('client_vat_id');

    if ($req_einvoicing) {
        $nb_users = count($req_einvoicing->users);
        $me = $req_einvoicing->users[$_SESSION['user_id']]->show_table ?? false;
        $nb = $req_einvoicing->show_table ?? 0;
        $ln = 'user' . (($nb ?: $nb_users) > 1 ? 's' : '');
        $user_toggle = ($req_einvoicing->show_table
            ? ($me ? 'danger' : 'warning')
            : 'default'
        ) . ($me ? '" aria-expanded="true' : '" collapsed" aria-expanded="false');
    }

    $einvoicingTip = $req_einvoicing ? ' data-toggle="tooltip" data-placement="bottom" title="e-' . trans('invoicing') . ' (' : '';
    $einvoicingReq = $req_einvoicing ? $einvoicingTip . trans('required_field') . ')"' : '';
    $einvoicingB2B = $req_einvoicing ? $einvoicingTip . 'B2B ' . trans('required_field') . ')"' : '';
    $einvoicingOpt = $req_einvoicing ? $einvoicingTip . trans('optional') . ')"' : '';
@endphp

<script type="text/javascript">
    const switch_fa_toggle = function(id) {
        const f = $('#' + id);
        f.toggleClass('fa-user').toggleClass('fa-users');
    };

    $(function() {
        $('#client_country').select2({
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

        <input type="hidden" name="is_update" value="{{ $this->mdl_clients->form_value('is_update') ? '1' : '0' }}">

        <div class="flex flex-wrap -mx-4">
            {{-- PERSONAL INFORMATION --}}
            <div class="w-full px-4 col-sm-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                    <div
                        class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex flex-wrap gap-4 items-center">
                        @lang('personal_information')
                        <div class="float-right">
                            <label for="client_active" class="control-label">
                                @lang('active_client')
                                <input id="client_active" name="client_active" type="checkbox"
                                       value="1" {!! $active !!}>
                            </label>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="mb-4">
                            <label for="client_name">@lang('client_name')</label>
                            <input id="client_name" name="client_name" type="text"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm"
                                   autofocus
                                   value="{{ $this->mdl_clients->form_value('client_name', true) }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="client_surname">@lang('client_surname_optional')</label>
                            <input id="client_surname" name="client_surname" type="text"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm"
                                   value="{{ $this->mdl_clients->form_value('client_surname', true) }}">
                        </div>

                        <div class="mb-4" {!! $itsCompany ? $einvoicingB2B : $einvoicingOpt !!}>
                            <label for="client_company">
                                @lang('client_company') ({{ trans($itsCompany ? 'required_field' : 'optional') }})
                            </label>
                            <input id="client_company" name="client_company" type="text"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm"
                                   value="{{ $this->mdl_clients->form_value('client_company', true) }}">
                        </div>

                        <div class="mb-4">
                            <label for="client_language">@lang('lang')</label>
                            <select name="client_language" id="client_language"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm simple-select">
                                <option value="system">@lang('use_system_language')</option>
                                @php $client_lang = $this->mdl_clients->form_value('client_language'); @endphp
                                @foreach($languages as $language)
                                    <option value="{{ $language }}" @selected($client_lang === $language)>
                                        {{ ucfirst($language) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- EINVOICING --}}
            @if($req_einvoicing)
                <div class="w-full px-4 col-sm-6">
                    <div
                        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                        <div
                            class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            e-@lang('invoicing')
                        </div>

                        <div class="p-6">
                            @if($xml_templates)
                                @if($client_id)
                                    @include('clients.partial_client_einvoicing')
                                @else
                                    <div
                                        class="p-4 mb-4 text-yellow-700 bg-yellow-100 border border-yellow-200 rounded-lg small">
                                        <i class="fa fa-exclamation-triangle fa-2x"></i>&nbsp;
                                        @lang('einvoicing_no_enabled_hint')
                                    </div>
                                @endif
                            @else
                                <div class="p-4 mb-4 text-cyan-700 bg-cyan-100 border border-cyan-200 rounded-lg small">
                                    <i class="fa fa-info"></i>&nbsp;
                                    @lang('einvoicing_how_enable_hint')
                                    <a href="https://github.com/InvoicePlane/InvoicePlane-e-invoices" target="_blank">
                                        InvoicePlane-e-invoices
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- ADDRESS --}}
            <div class="w-full px-4 col-sm-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        @lang('address')
                    </div>

                    <div class="p-6">
                        @include('clients.partials.address', [
                            'mdl_clients' => $this->mdl_clients,
                            'einvoicingReq' => $einvoicingReq,
                            'einvoicingOpt' => $einvoicingOpt,
                            'einvoicingB2B' => $einvoicingB2B,
                            'countries' => $countries,
                            'selected_country' => $selected_country,
                            'custom_fields' => $custom_fields,
                            'custom_values' => $custom_values
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
