
<script>
    $(function () {
        $('#enter-payment').modal('show');

        $('#enter-payment').on('shown', function () {
            $('#payment_amount').focus();
        });

        // Select2 for all select inputs
        $(".simple-select").select2();

        $('#btn_modal_payment_submit').click(function () {
            $.post("{{ url('payments/ajax/add');
?>", {
                    invoice_id: $('#invoice_id').val(),
                    payment_amount: $('#payment_amount').val(),
                    payment_method_id: $('#payment_method_id').val(),
                    payment_date: $('#payment_date').val(),
                    payment_note: $('#payment_note').val()
                },
                function (data) {
                    var response = json_parse(data, {{ (int) IP_DEBUG }});
            @if(response.success === 1) {
                // The validation was successful and payment was added
                @if($('#payment_cf_exist').val() === 'yes') {
                    // There are payment custom fields, display the payment form
                    // to allow completing the custom fields
                    window.location = "{{ url('payments/form') }}/" + response.payment_id;
                } else {
                    // There are no payment custom fields, return to invoice view
                    window.location = "{{ $_SERVER['HTTP_REFERER'] }}";
                }
            } else {
                // The validation was not successful
                $('.control-group').removeClass('has-error');
                for (var key in response.validation_errors) {
                    @if(response.validation_errors.hasOwnProperty(key)) {
                        $('#' + key).parent().parent().addClass('has-error');
                    }
                }
            }
        });
    });
    })
    ;
</script>

<div id="enter-payment" class="modal w-full px-4 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal_enter_payment" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3>@lang('enter_payment')</h3>
        </div>

        <div class="modal-body">
            <form>

                <input type="hidden" name="invoice_id" id="invoice_id" value="{{ $invoice_id " }}>

                <div class="mb-4">
                    <label for="payment_amount">@lang('amount')</label>

                    <div class="controls">
                        <input type="text" name="payment_amount" id="payment_amount" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                               value="{{ isset($invoice_balance) ? format_amount($invoice_balance) : '' " }}>
                    </div>
                </div>

                <div class="mb-4 has-feedback">

                    <label class="payment_date">@lang('payment_date')</label>

                    <div class="input-group">
                        <input name="payment_date" id="payment_date"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors datepicker"
                               value="{{ date(date_format_setting()) " }}>
                        <span class="input-group-addon">
                            <i class="fa fa-calendar fa-fw"></i>
                        </span>
                    </div>

                </div>

                <div class="mb-4">
                    <label for="payment_method_id">@lang('payment_method')</label>

                    <div class="controls">

@if($this->mdl_payments->form_value('payment_method_id'))
                        <input type="hidden" name="payment_method_id" class="hidden"
                               value="{{ $this->mdl_payments->form_value('payment_method_id') " }}>
                        @endif
                        <select name="payment_method_id" id="payment_method_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select"
                            {{ empty($invoice_payment_method) ? '' : 'disabled="disabled"' }}>
                            <option value="">@lang('none')</option>
                            @foreach($payment_methods as $payment_method)
                            <option value="{{ $payment_method->payment_method_id }}"
                            @php
                                check_select(isset($invoice_payment_method) && $invoice_payment_method == $payment_method->payment_method_id) }}>
                                                            {!! $payment_method->payment_method_name !!}
                            </option>
                            <?php
@endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="payment_note">@lang('note')</label>

                            <div class="controls">
                                <textarea name="payment_note" id="payment_note" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"></textarea>
                            </div>
                    </div>

                    <!-- Add a hidden input field to pass whether payment custom fields have been create -->
                    <input type="hidden" name="payment_cf_exist" id="payment_cf_exist" value="{{ $payment_cf_exist " }}>

            </form>
        </div>

        <div class="modal-footer">
            <div class="inline-flex rounded-md shadow-sm">
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors" id="btn_modal_payment_submit" type="button">
                    <i class="fa fa-check"></i>
                    @lang('submit')
                </button>
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                    @lang('cancel')
                </button>
            </div>
        </div>
    </div>

</div>
