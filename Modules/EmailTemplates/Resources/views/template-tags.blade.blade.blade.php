@php namespace Modules\Emailtemplates\Views; @endphp
<div class="panel panel-default">
    <div class="panel-heading">@@lang('email_template_tags')</div>
    <div class="panel-body">

        <p class="small">@@lang('email_template_tags_instructions')</p>

        <div class="form-group">
            <label for="tags_client">@@lang('client')</label>
            <select id="tags_client" class="tag-select form-control">
                <option value="{{{client_name}}}">
                    @@lang('client_name')
                </option>
                <option value="{{{client_surname}}}">
                    @@lang('client_surname')
                </option>
                <optgroup label="@@lang('address')">
                    <option value="{{{client_address_1}}}">
                        @@lang('street_address')
                    </option>
                    <option value="{{{client_address_2}}}">
                        @@lang('street_address_2')
                    </option>
                    <option value="{{{client_city}}}">
                        @@lang('city')
                    </option>
                    <option value="{{{client_state}}}">
                        @@lang('state')
                    </option>
                    <option value="{{{client_zip}}}">
                        @@lang('zip')
                    </option>
                    <option value="{{{client_country}}}">
                        @@lang('country')
                    </option>
                </optgroup>
                <optgroup label="@@lang('contact_information')">
                    <option value="{{{client_phone}}}">
                        @@lang('phone')
                    </option>
                    <option value="{{{client_fax}}}">
                        @@lang('fax')
                    </option>
                    <option value="{{{client_mobile}}}">
                        @@lang('mobile')
                    </option>
                    <option value="{{{client_email}}}">
                        @@lang('email')
                    </option>
                    <option value="{{{client_web}}}">
                        @@lang('web_address')
                    </option>
                </optgroup>
                <optgroup label="@@lang('tax_information')">
                    <option value="{{{client_vat_id}}}">
                        @@lang('vat_id')
                    </option>
                    <option value="{{{client_tax_code}}}">
                        @@lang('tax_code')
                    </option>
                </optgroup>
@php $sumex = get_setting('sumex') == '1';
if ($sumex) {
    @endphp
                <optgroup label="@@lang('sumex_information')">
                    <option value="{{{client_avs}}}">
                        @@lang('sumex_ssn')
                    </option>
                    <option value="{{{client_insurednumber}}}">
                        @@lang('sumex_insurednumber')
                    </option>
                    <option value="{{{client_weka}}}">
                        @@lang('sumex_veka')
                    </option>
                </optgroup>
@php
}
if ($custom_fields['ip_client_custom']) {
    @endphp
                <optgroup label="@@lang('custom_fields')">
                    @php
    foreach ($custom_fields['ip_client_custom'] as $custom) {
        @endphp
                        <option value="{{{{{ 'ip_cf_' . $custom->custom_field_id }}}}}">
                            {{ $custom->custom_field_label . ' (ID ' . $custom->custom_field_id . ')' }}
                        </option>
                    @php
    }
    @endphp
                </optgroup>
@php
} @endphp
            </select>
        </div>

        <div class="form-group">
            <label for="tags_user">@@lang('user')</label>
            <select id="tags_user" class="tag-select form-control">
                <option value="{{{user_name}}}">
                    @@lang('name')
                </option>
                <option value="{{{user_company}}}">
                    @@lang('company')
                </option>
                <optgroup label="@@lang('address')">
                    <option value="{{{user_address_1}}}">
                        @@lang('street_address')
                    </option>
                    <option value="{{{user_address_2}}}">
                        @@lang('street_address_2')
                    </option>
                    <option value="{{{user_city}}}">
                        @@lang('city')
                    </option>
                    <option value="{{{user_state}}}">
                        @@lang('state')
                    </option>
                    <option value="{{{user_zip}}}">
                        @@lang('zip')
                    </option>
                    <option value="{{{user_country}}}">
                        @@lang('country')
                    </option>
                </optgroup>
                <optgroup label="@@lang('contact_information')">
                    <option value="{{{user_phone}}}">
                        @@lang('phone')
                    </option>
                    <option value="{{{user_fax}}}">
                        @@lang('fax')
                    </option>
                    <option value="{{{user_mobile}}}">
                        @@lang('mobile')
                    </option>
                    <option value="{{{user_email}}}">
                        @@lang('email')
                    </option>
                    <option value="{{{user_web}}}">
                        @@lang('web_address')
                    </option>
                </optgroup>
                <optgroup label="@@lang('tax_information')">
                    <option value="{{{user_vat_id}}}">
                        @@lang('vat_id')
                    </option>
                    <option value="{{{user_tax_code}}}">
                        @@lang('tax_code')
                    </option>
                </optgroup>
                <optgroup label="@@lang('bank_information')">
                    <option value="{{{user_bank}}}">
                        @@lang('bank')
                    </option>
                    <option value="{{{user_iban}}}">
                        IBAN
                    </option>
                    <option value="{{{user_bic}}}">
                        BIC
                    </option>
                </optgroup>
@php if ($sumex) {
    @endphp
                <optgroup label="@@lang('sumex_information')">
                    <option value="{{{user_subscribernumber}}}">
                        @@lang('user_subscriber_number')
                    </option>
                    <option value="{{{user_gln}}}">
                        @@lang('gln')
                    </option>
                    <option value="{{{user_rcc}}}">
                        @@lang('sumex_rcc')
                    </option>
                </optgroup>
@php
}
if ($custom_fields['ip_user_custom']) {
    @endphp
                <optgroup label="@@lang('custom_fields')">
                    @php
    foreach ($custom_fields['ip_user_custom'] as $custom) {
        @endphp
                        <option value="{{{{{ 'ip_cf_' . $custom->custom_field_id }}}}}">
                            {{ $custom->custom_field_label . ' (ID ' . $custom->custom_field_id . ')' }}
                        </option>
                    @php
    }
    @endphp
                </optgroup>
@php
} @endphp
            </select>
        </div>

        @php $this->layout->loadView('email_templates/template-tags-invoices'); @endphp

        <div class="form-group">
            <label for="tags_quote">@@lang('quotes')</label>
            <select id="tags_quote" class="tag-select form-control">
                <option value="{{{quote_number}}}">
                    @@lang('id')
                </option>
                <optgroup label="@@lang('quote_dates')">
                    <option value="{{{quote_date_created}}}">
                        @@lang('quote_date')
                    </option>
                    <option value="{{{quote_date_expires}}}">
                        @@lang('expires')
                    </option>
                </optgroup>
                <optgroup label="@@lang('quote_amounts')">
                    <option value="{{{quote_item_subtotal}}}">
                        @@lang('subtotal')
                    </option>
                    <option value="{{{quote_tax_total}}}">
                        @@lang('quote_tax')
                    </option>
                    <option value="{{{quote_item_discount}}}">
                        @@lang('discount')
                    </option>
                    <option value="{{{quote_total}}}">
                        @@lang('total')
                    </option>
                </optgroup>

                <optgroup label="@@lang('extra_information')">
                    <option value="{{{quote_guest_url}}}">
                        @@lang('guest_url')
                    </option>
                </optgroup>
@php if ($custom_fields['ip_quote_custom']) {
    @endphp

                <optgroup label="@@lang('custom_fields')">
                    @php
    foreach ($custom_fields['ip_quote_custom'] as $custom) {
        @endphp
                        <option value="{{{{{ 'ip_cf_' . $custom->custom_field_id }}}}}">
                            {{ $custom->custom_field_label . ' (ID ' . $custom->custom_field_id . ')' }}
                        </option>
                    @php
    }
    @endphp
                </optgroup>
@php
} @endphp
            </select>
        </div>
@php if ($sumex) {
    @endphp
        <div class="form-group">
            <label for="tags_sumex">@@lang('invoice_sumex')</label>
            <select id="tags_sumex" class="tag-select form-control">
                <option value="{{{sumex_reason}}}">
                    @@lang('reason')
                </option>
                <option value="{{{sumex_diagnosis}}}">
                    @@lang('invoice_sumex_diagnosis')
                </option>
                <option value="{{{sumex_observations}}}">
                    @@lang('sumex_observations')
                </option>
                <option value="{{{sumex_treatmentstart}}}">
                    @@lang('treatment_start')
                </option>
                <option value="{{{sumex_treatmentend}}}">
                    @@lang('treatment_end')
                </option>
                <option value="{{{sumex_casedate}}}">
                    @@lang('case_date')
                </option>
                <option value="{{{sumex_casenumber}}}">
                    @@lang('case_number')
                </option>
            </select>
        </div>
<?php
} @endphp
    </div>
</div>
<?php 
