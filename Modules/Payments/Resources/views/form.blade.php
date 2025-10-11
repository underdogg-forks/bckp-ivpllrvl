
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

    @csrf

    @if($payment_id)
        <input type="hidden" name="payment_id" value="{{ $payment_id }}">
    @endif

    <div id="headerbar">
        <h1 class="headerbar-title">@lang('payment_form') }}</h1>
        @php $this->layout->loadView('layout/header_buttons', ['attribute_cancel' => 'onclick="window.location.href = `' . site_url('payments') . '`;"'])
    </div>

    <div id="content">

        @include('layout.alerts')

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="invoice_id" class="control-label">@lang('invoice')</label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <select name="invoice_id" id="invoice_id" class="form-control simple-select" required>
                    @if(!$payment_id)
                        {
                        foreach ($open_invoices as $invoice)
                        <option value="{{ $invoice->invoice_id }}"
                            @php
                                check_select($this->mdl_payments->form_value('invoice_id'), $invoice->invoice_id)>
                            {{ $invoice->invoice_number . ' - ' . htmlsc(format_client($invoice)) . ' - ' . format_currency($invoice->invoice_balance) }}
                        </option>
                        @php
                            }
                            // End foreach
                        } else {

                        <option value="{{ $payment->invoice_id }}">
                            {{ $payment->invoice_number . ' - ' . htmlsc(format_client($payment)) . ' - ' . format_currency($payment->invoice_balance) }}
                        </option>
                    @endif
                </select>
            </div>
        </div>

        <div class="form-group has-feedback">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_date" class="control-label">@lang('date')</label>
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
                <label for="payment_amount" class="control-label">@lang('amount')</label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="payment_amount" id="payment_amount" class="form-control"
                       value="{{ format_amount($this->mdl_payments->form_value('payment_amount')) }}" required>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_method_id" class="control-label">
                    @lang('payment_method')
                </label>
            </div>
            <div class="col-xs-12 col-sm-6 payment-method-wrapper">

@if($this->mdl_payments->form_value('payment_method_id'))
                <input type="hidden" name="payment_method_id" class="hidden"
                       value="{{ $this->mdl_payments->form_value('payment_method_id') }}">
                @endif

                <select id="payment_method_id" name="payment_method_id"
                        class="form-control simple-select" data-minimum-results-for-search="Infinity"
                    {{ $this->mdl_payments->form_value('payment_method_id') ? 'disabled="disabled"' : '' }}>
                    @foreach($payment_methods as $payment_method)
                        <option value="{{ $payment_method->payment_method_id }}"
                            {{ $this->mdl_payments->form_value('payment_method_id') == $payment_method->payment_method_id ? 'selected="selected"' : '' }}>
                            {{ $payment_method->payment_method_name }}
                        </option>@endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_note" class="control-label">@lang('note')</label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <textarea name="payment_note"
                          class="form-control">{{ $this->mdl_payments->form_value('payment_note', true) }}</textarea>
            </div>

        </div>

        @php $classes = ['col-xs-12 col-sm-2 text-right text-left-xs', 'col-xs-12 col-sm-6', 'control-label', 'form-group'];
foreach ($custom_fields as $custom_field) {
    print_field($this->mdl_payments, $custom_field, $custom_values, $classes[0], $classes[1], $classes[2], $classes[3]);
}

    </div>

</form>
    