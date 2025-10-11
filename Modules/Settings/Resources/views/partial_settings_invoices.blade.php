
<div class="row">
    <div class="col-xs-12 col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                @lang('invoices')
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[default_invoice_group]">
                                @lang('default_invoice_group')
                            </label>
                            <select name="settings[default_invoice_group]" id="settings[default_invoice_group]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                <option value="">@lang('none')</option>
                                @foreach($invoice_groups as $invoice_group)
                                <option value="{{ $invoice_group->invoice_group_id }}"
                                    @php
                                        check_select(get_setting('default_invoice_group'), $invoice_group->invoice_group_id)>
                                    {{ $invoice_group->invoice_group_name }}
                                </option>@endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="settings[default_invoice_terms]">
                                @lang('default_terms')
                            </label>
                            <textarea name="settings[default_invoice_terms]" id="settings[default_invoice_terms]"
                                      class="form-control" rows="4"
                            >{{ get_setting('default_invoice_terms', '', true) }}</textarea>
                        </div>

                    </div>
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[invoice_default_payment_method]">
                                @lang('default_payment_method')
                            </label>
                            <select name="settings[invoice_default_payment_method]" class="form-control simple-select"
                                    id="settings[invoice_default_payment_method]"
                                    data-minimum-results-for-search="Infinity">
                                <option value="">@lang('none')</option>
                                @foreach($payment_methods as $payment_method)
                                <option value="{{ $payment_method->payment_method_id }}"
                                    @php
                                        check_select($payment_method->payment_method_id, get_setting('invoice_default_payment_method'))>
                                    {{ $payment_method->payment_method_name }}
                                </option>@endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="settings[invoices_due_after]">
                                @lang('invoices_due_after')
                            </label>
                            <input type="number" name="settings[invoices_due_after]" id="settings[invoices_due_after]"
                                   class="form-control" value="{{ get_setting('invoices_due_after') " }}>
                        </div>

                        <div class="form-group">
                            <label for="settings[generate_invoice_number_for_draft]">
                                @lang('generate_invoice_number_for_draft')
                            </label>
                            <select name="settings[generate_invoice_number_for_draft]"
                                    class="form-control simple-select"
                                    id="settings[generate_invoice_number_for_draft]"
                                    data-minimum-results-for-search="Infinity">
                                <option value="0">
                                    @lang('no')
                                </option>
                                <option
                                    value="1" @php check_select(get_setting('generate_invoice_number_for_draft'), '1')>
                                    @lang('yes')
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="settings[einvoicing]">
                                @lang('einvoicing_enable')
                            </label>
                            <select name="settings[einvoicing]" id="settings[einvoicing]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                <option value="0">
                                    @lang('no')
                                </option>
                                <option value="1" @php check_select(get_setting('einvoicing'), '1')>
                                    @lang('yes')
                                </option>
                            </select>
                            <p class="help-block">
                                @lang('einvoicing_enable_help')
                                <a href="https://github.com/InvoicePlane/InvoicePlane-e-invoices" target="_blank">InvoicePlane-e-invoices</a>
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                @lang('pdf_settings')
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[mark_invoices_sent_pdf]">
                                @lang('mark_invoices_sent_pdf')
                            </label>
                            <select name="settings[mark_invoices_sent_pdf]" id="settings[mark_invoices_sent_pdf]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                <option value="0">
                                    @lang('no')
                                </option>
                                <option
                                    value="1" @php check_select(get_setting('mark_invoices_sent_pdf'), '1')>
                                    @lang('yes')
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="settings[invoice_pre_password]">
                                @lang('invoice_pre_password')
                            </label>
                            <input type="text" name="settings[invoice_pre_password]" id="settings[invoice_pre_password]"
                                   class="form-control"
                                   value="{{ get_setting('invoice_pre_password', '', true) " }}>
                        </div>

                    </div>
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[pdf_watermark]">
                                @lang('pdf_watermark')
                            </label>
                            <select name="settings[pdf_watermark]" id="settings[pdf_watermark]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                <option value="0">
                                    @lang('no')
                                </option>
                                <option value="1" @php check_select(get_setting('pdf_watermark'), '1')>
                                    @lang('yes')
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>@lang('invoice_logo')</label>
                            @if(get_setting('invoice_logo'))
                            <br/>
                            <img class="personal_logo"
                                 src="{{ url() }}uploads/{{ get_setting('invoice_logo') " }}>
                            <br>
                            {{ anchor('settings/remove_logo/invoice', trans('remove_logo')) }}<br/>@endforeach
                            <input type="file" name="invoice_logo" size="40" class="form-control"/>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                @lang('invoice_templates')
            </div>
            <div class="panel-body">
                <div class="help-block">
                    @lang('invoice_templates_info')
                </div>
                <div class="row">
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[pdf_invoice_template]">
                                @lang('default_pdf_template')
                            </label>
                            <select name="settings[pdf_invoice_template]" id="settings[pdf_invoice_template]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                <option value="">@lang('none')</option>
                                @foreach($pdf_invoice_templates as $invoice_template)
                                <option value="{{ $invoice_template }}"
                                    @php
                                        check_select(get_setting('pdf_invoice_template'), $invoice_template)>
                                    {{ $invoice_template }}
                                </option>@endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="settings[pdf_invoice_template_paid]">
                                @lang('pdf_template_paid')
                            </label>
                            <select name="settings[pdf_invoice_template_paid]" id="settings[pdf_invoice_template_paid]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                <option value="">@lang('none')</option>
                                @foreach($pdf_invoice_templates as $invoice_template)
                                <option value="{{ $invoice_template }}"
                                    @php
                                        check_select(get_setting('pdf_invoice_template_paid'), $invoice_template)>
                                    {{ $invoice_template }}
                                </option>@endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="settings[pdf_invoice_template_overdue]">
                                @lang('pdf_template_overdue')
                            </label>
                            <select name="settings[pdf_invoice_template_overdue]" class="form-control simple-select"
                                    id="settings[pdf_invoice_template_overdue]"
                                    data-minimum-results-for-search="Infinity">
                                <option value="">@lang('none')</option>
                                @foreach($pdf_invoice_templates as $invoice_template)
                                <option value="{{ $invoice_template }}"
                                    @php
                                        check_select(get_setting('pdf_invoice_template_overdue'), $invoice_template)>
                                    {{ $invoice_template }}
                                </option>@endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="settings[public_invoice_template]">
                                @lang('default_public_template')
                            </label>
                            <select name="settings[public_invoice_template]" id="settings[public_invoice_template]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                <option value="">@lang('none')</option>
                                @foreach($public_invoice_templates as $invoice_template)
                                <option value="{{ $invoice_template }}"
                                    @php
                                        check_select(get_setting('public_invoice_template'), $invoice_template)>
                                    {{ $invoice_template }}
                                </option>@endforeach
                            </select>
                        </div>

                    </div>
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[email_invoice_template]">
                                @lang('default_email_template')
                            </label>
                            <select name="settings[email_invoice_template]" id="settings[email_invoice_template]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                <option value="">@lang('none')</option>
                                @foreach($email_templates_invoice as $email_template)
                                <option value="{{ $email_template->email_template_id }}"
                                    @php
                                        check_select(get_setting('email_invoice_template'), $email_template->email_template_id)>
                                    {{ $email_template->email_template_title }}
                                </option>@endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="settings[email_invoice_template_paid]">
                                @lang('email_template_paid')
                            </label>
                            <select name="settings[email_invoice_template_paid]"
                                    id="settings[email_invoice_template_paid]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                <option value="">@lang('none')</option>
                                @foreach($email_templates_invoice as $email_template)
                                <option value="{{ $email_template->email_template_id }}"
                                    @php
                                        check_select(get_setting('email_invoice_template_paid'), $email_template->email_template_id)>
                                    {{ $email_template->email_template_title }}
                                </option>@endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="settings[email_invoice_template_overdue]">
                                @lang('email_template_overdue')
                            </label>
                            <select name="settings[email_invoice_template_overdue]" class="form-control simple-select"
                                    id="settings[email_invoice_template_overdue]"
                                    data-minimum-results-for-search="Infinity">
                                <option value="">@lang('none')</option>
                                @foreach($email_templates_invoice as $email_template)
                                <option value="{{ $email_template->email_template_id }}"
                                    @php
                                        check_select(get_setting('email_invoice_template_overdue'), $email_template->email_template_id)>
                                    {{ $email_template->email_template_title }}
                                </option>@endforeach
                            </select>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[pdf_invoice_footer]">
                                @lang('pdf_invoice_footer')
                            </label>
                            <textarea name="settings[pdf_invoice_footer]" id="settings[pdf_invoice_footer]"
                                      class="form-control no-margin">{{ get_setting('pdf_invoice_footer', '', true) }}</textarea>
                            <p class="help-block">@lang('pdf_invoice_footer_hint')</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default" id="panel-qr-code-settings">
            <div class="panel-heading">
                @lang('qr_code_settings')
            </div>
            <div class="panel-body">

                @php $qr_code = get_setting('qr_code')
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input
                                type="hidden"
                                name="settings[qr_code]"
                                value="0"
                            >
                            <input
                                type="checkbox"
                                name="settings[qr_code]"
                                id="settings[qr_code]"
                                value="1"
                                @php check_select($qr_code, 1, '==', true);
                            >
                            @lang('qr_code_settings_enable')
                        </label>
                        <p class="help-block">@lang('qr_code_settings_enable_hint')</p>
                    </div>
                </div>

                <div class="row {{ $qr_code ? '' : 'hidden' " }}>
                    <div class="col-xs-12">
                        <p class="alert alert-info no-padding">
                            <i class="fa fa-info"></i>@lang('qr_code_settings_enable_hint_users')&nbsp;<i
                                class="fa fa-qrcode"></i>
                        </p>
                    </div>
                </div>

                <div class="row {{ $qr_code ? '' : 'hidden' " }}>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[qr_code_recipient]">
                                @lang('qr_code_settings_recipient')
                            </label>
                            <input
                                type="text"
                                name="settings[qr_code_recipient]"
                                id="settings[qr_code_recipient]"
                                class="form-control"
                                placeholder="{!! trans('company') !!}"
                                value="{{ get_setting('qr_code_recipient') }}"
                            >
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[qr_code_iban]">
                                @lang('qr_code_settings_iban')
                            </label>
                            <input
                                type="text"
                                name="settings[qr_code_iban]"
                                id="settings[qr_code_iban]"
                                class="form-control"
                                value="{{ get_setting('qr_code_iban') }}"
                            >
                        </div>
                    </div>
                </div>

                <div class="row {{ $qr_code ? '' : 'hidden' " }}>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[qr_code_bic]">
                                @lang('qr_code_settings_bic')
                            </label>
                            <input
                                type="text"
                                name="settings[qr_code_bic]"
                                id="settings[qr_code_bic]"
                                class="form-control"
                                value="{{ get_setting('qr_code_bic') }}"
                            >
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[qr_code_remittance_text]">
                                @lang('qr_code_settings_remittance_text')
                            </label>
                            <input
                                type="text"
                                name="settings[qr_code_remittance_text]"
                                id="settings[qr_code_remittance_text]"
                                class="form-control taggable"
                                value="{{ get_setting('qr_code_remittance_text') }}"
                                placeholder="{{{invoice_number}}}"
                            >
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                @lang('qr_code_settings_remittance_text_tags')
                            </div>
                            <div class="panel-body">
                                @include('email_templates.template-tags-invoices')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                @lang('email_settings')
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[automatic_email_on_recur]">
                                @lang('automatic_email_on_recur')
                            </label>
                            <select name="settings[automatic_email_on_recur]" id="settings[automatic_email_on_recur]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                <option value="0">
                                    @lang('no')
                                </option>
                                <option
                                    value="1" @php check_select(get_setting('automatic_email_on_recur'), '1')>
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
                @lang('other_settings')
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[read_only_toggle]">
                                @lang('set_to_read_only')
                            </label>
                            <select name="settings[read_only_toggle]" id="settings[read_only_toggle]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                <option value="2" @php check_select(get_setting('read_only_toggle'), '2')>
                                    @lang('sent')
                                </option>
                                <option value="3" @php check_select(get_setting('read_only_toggle'), '3')>
                                    @lang('viewed')
                                </option>
                                <option value="4" @php check_select(get_setting('read_only_toggle'), '4')>
                                    @lang('paid')
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[no_update_invoice_due_date_mail]">
                                @lang('no_update_invoice_due_date_mail')
                            </label>
                            <select name="settings[no_update_invoice_due_date_mail]" class="form-control simple-select"
                                    id="settings[no_update_invoice_due_date_mail]"
                                    data-minimum-results-for-search="Infinity">
                                <option
                                    value="1" @php check_select(get_setting('no_update_invoice_due_date_mail'), '1')>
                                    @lang('yes')
                                </option>
                                <option
                                    value="0" @php check_select(get_setting('no_update_invoice_due_date_mail'), '0')>
                                    @lang('no')
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @php $sumex = get_setting('sumex');
// Set in ipconfig OR is 1 (in db)
@if(SUMEX_SETTINGS || $sumex == '1') {

        <div class="panel panel-default">
            <div class="panel-heading">
                @lang('sumex_settings')
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[sumex]">
                                @lang('invoice_sumex')
                            </label>
                            <select name="settings[sumex]" id="settings[sumex]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                <option value="0">
                                    @lang('no')
                                </option>
                                <option value="1" @php
                                    check_select($sumex, '1')>
                                    @lang('yes')
                                </option>
                            </select>
                            <p class="help-block">@lang('invoice_sumex_help')</p>
                        </div>

                        <div class="form-group">
                            <label for="settings[sumex_sliptype]">
                                @lang('invoice_sumex_sliptype')
                            </label>
                            <select name="settings[sumex_sliptype]" id="settings[sumex_sliptype]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                @php
                                    $slipTypes = ['esr9', 'esrRed'];
                                    @foreach($slipTypes as $k => $v) {

                                <option value="{{ $k }}" @php
                                    check_select(get_setting('sumex_sliptype'), $k)>
                                    {{ _trans('invoice_sumex_sliptype-' . $v) }}</option>@endforeach
                            </select>
                            <p class="help-block">@lang('invoice_sumex_sliptype_help')</p>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[sumex_role]">
                                @lang('invoice_sumex_role')
                            </label>
                            <select name="settings[sumex_role]" id="settings[sumex_role]"
                                    class="form-control simple-select">
                                @php
                                    $roles = Modules\Core\Libraries\Sumex::ROLES;
                                    @foreach($roles as $k => $v) {

                                <option value="{{ $k }}" @php
                                    check_select(get_setting('sumex_role'), $k)>
                                    {{ _trans('invoice_sumex_role_' . $v) }}</option>@endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="settings[sumex_place]">
                                @lang('invoice_sumex_place')
                            </label>
                            <select name="settings[sumex_place]" id="settings[sumex_place]"
                                    class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                @php
                                    $places = Modules\Core\Libraries\Sumex::PLACES;
                                    @foreach($places as $k => $v) {

                                <option value="{{ $k }}" @php
                                    check_select(get_setting('sumex_place'), $k)>
                                    {{ _trans('invoice_sumex_place_' . $v) }}</option>@endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="settings[sumex_canton]">
                                @lang('invoice_sumex_canton')
                            </label>
                            <select name="settings[sumex_canton]" id="settings[sumex_canton]"
                                    class="form-control simple-select">
                                @php
                                    $cantons = Modules\Core\Libraries\Sumex::CANTONS;
                                    @foreach($cantons as $k => $v) {

                                <option value="{{ $k }}" @php
                                    check_select(get_setting('sumex_canton'), $k)>
                                    {{ $v }}
                                </option>@endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
}
// End If Modules\Core\Libraries\Sumex

</div >
</div >
