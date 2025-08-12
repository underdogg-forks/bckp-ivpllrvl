@php namespace Modules\Payments\Views; @endphp
<script>
    $(function () {
        var $invoice_id = $('#invoice_id');
        $invoice_id.focus();

        amounts = json_parse('{{ $amounts;
?>', {{ (int) IP_DEBUG }});
        invoice_payment_methods = json_parse('{{ $invoice_payment_methods }}', {{ (int) IP_DEBUG }});
        $invoice_id.change(function () {
            var invoice_identifier = "invoice" + $('#invoice_id').val();
            $('#payment_amount').val(amounts[invoice_identifier].replace("&nbsp;", " "));
            $('#payment_method_id').val(invoice_payment_methods[invoice_identifier]).trigger('change');

            if (invoice_payment_methods[invoice_identifier] != 0) {
                $('.payment-method-wrapper').append("<input type='hidden' name='payment_method_id' id='payment-method-id-hidden' class='hidden' value='" + invoice_payment_methods[invoice_identifier] + "'>");
                $('#payment_method_id').prop('disabled', true);
            } else {
                $('#payment-method-id-hidden').remove();
                $('#payment_method_id').prop('disabled', false);
            }
        });
    });
</script>

<form method="post" class="form-horizontal">

    @php _csrf_field(); @endphp

@php if ($payment_id) {
    @endphp
    <input type="hidden" name="payment_id" value="{{ $payment_id }}">
@php
} @endphp

    <div id="headerbar">
        <h1 class="headerbar-title">@php @@lang('payment_form') }}</h1>
        @php $this->layout->loadView('layout/header_buttons', ['attribute_cancel' => 'onclick="window.location.href = `' . site_url('payments') . '`;"']); @endphp
    </div>

    <div id="content">

        @php $this->layout->loadView('layout/alerts'); @endphp

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="invoice_id" class="control-label">@@lang('invoice')</label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <select name="invoice_id" id="invoice_id" class="form-control simple-select" required>
@php if (!$payment_id) {
    foreach ($open_invoices as $invoice) {
        @endphp
                        <option value="{{ $invoice->invoice_id }}"
                                @php
        check_select($this->mdl_payments->form_value('invoice_id'), $invoice->invoice_id);
        @endphp>
                            {{ $invoice->invoice_number . ' - ' . htmlsc(format_client($invoice)) . ' - ' . format_currency($invoice->invoice_balance) }}
                        </option>
@php
    }
    // End foreach
} else {
    @endphp
                    <option value="{{ $payment->invoice_id }}">
                        {{ $payment->invoice_number . ' - ' . htmlsc(format_client($payment)) . ' - ' . format_currency($payment->invoice_balance) }}
                    </option>
@php
} @endphp
                </select>
            </div>
        </div>

        <div class="form-group has-feedback">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_date" class="control-label">@@lang('date')</label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="input-group">
                    <input name="payment_date" id="payment_date"
                           class="form-control datepicker"
                           value="{{ date_from_mysql($this->mdl_payments->form_value('payment_date')) }}" required>
                    <span class="input-group-addon">
                        <i class="fa fa-calendar fa-fw"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_amount" class="control-label">@@lang('amount')</label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="payment_amount" id="payment_amount" class="form-control"
                       value="{{ format_amount($this->mdl_payments->form_value('payment_amount')) }}" required>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_method_id" class="control-label">
                    @@lang('payment_method')
                </label>
            </div>
            <div class="col-xs-12 col-sm-6 payment-method-wrapper">

@php // Add a hidden input field if a payment method was set to pass the disabled attribute
if ($this->mdl_payments->form_value('payment_method_id')) {
    @endphp
                    <input type="hidden" name="payment_method_id" class="hidden"
                           value="{{ $this->mdl_payments->form_value('payment_method_id') }}">
@php
} @endphp

                <select id="payment_method_id" name="payment_method_id"
                        class="form-control simple-select" data-minimum-results-for-search="Infinity"
                        {{ $this->mdl_payments->form_value('payment_method_id') ? 'disabled="disabled"' : '' }}>
@php foreach ($payment_methods as $payment_method) {
    @endphp
                    <option value="{{ $payment_method->payment_method_id }}"
                        {{ $this->mdl_payments->form_value('payment_method_id') == $payment_method->payment_method_id ? 'selected="selected"' : '' }}>
                        {{ $payment_method->payment_method_name }}
                    </option>
<?php
} @endphp
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_note" class="control-label">@@lang('note')</label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <textarea name="payment_note"
                          class="form-control">{{ $this->mdl_payments->form_value('payment_note', true) }}</textarea>
            </div>

        </div>

@php $classes = ['col-xs-12 col-sm-2 text-right text-left-xs', 'col-xs-12 col-sm-6', 'control-label', 'form-group'];
foreach ($custom_fields as $custom_field) {
    print_field($this->mdl_payments, $custom_field, $custom_values, $classes[0], $classes[1], $classes[2], $classes[3]);
} @endphp

    </div>

</form>
<?php 
