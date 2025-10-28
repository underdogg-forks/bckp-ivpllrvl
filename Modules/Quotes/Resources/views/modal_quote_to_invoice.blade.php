
<script>
    $(function () {
        // Display the create quote modal
        $('#modal_quote_to_invoice').modal('show');

        // Select2 for all select inputs
        $(".simple-select").select2();

        // Creates the invoice
        $('#quote_to_invoice_confirm').click(function () {
                show_loader(); // Show spinner
                $.post("{{ url('quotes/ajax/quote_to_invoice');
?>", {
                    legacy_calculation: legacy_calculation, // Automatic. From meta (see script)
                    quote_id: {{ $quote_id }},
                client_id: $('#client_id').val(),
                    invoice_date_created
            :
                $('#invoice_date_created').val(),
                    invoice_time_created
            :
                '{{ date('H:i:s') }}',
                    invoice_group_id
            :
                $('#invoice_group_id').val(),
                    invoice_password
            :
                $('#invoice_password').val(),
                    user_id
            :
                $('#user_id').val()
            },
            function (data) {
                var response = json_parse(data, {{ (int) IP_DEBUG }});
                @if(response.success === 1) {
                    window.location = "{{ url('invoices/view') }}/" + response.invoice_id;
                } else {
                    // The validation was not successful
                    close_loader();
                    $('.control-group').removeClass('has-error');
                    for (var key in response.validation_errors) {
                        $('#' + key).parent().parent().addClass('has-error');
                    }
                }
            }
        );
    });
    })
    ;
</script>

<div id="modal_quote_to_invoice" class="modal modal-lg" role="dialog" aria-labelledby="modal_quote_to_invoice"
     aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">@lang('quote_to_invoice')</h4>
        </div>
        <div class="modal-body">

            <input type="hidden" name="client_id" id="client_id"
                   value="{{ $quote->client_id " }}>
            <input type="hidden" name="user_id" id="user_id"
                   value="{{ $quote->user_id " }}>

            <div class="mb-4 has-feedback">
                <label for="invoice_date_created">
                    @lang('invoice_date')
                </label>

                <div class="input-group">
                    <input name="invoice_date_created" id="invoice_date_created"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors datepicker">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar fa-fw"></i>
                    </span>
                </div>
            </div>

            <div class="mb-4">
                <label for="invoice_password">@lang('invoice_password')</label>
                <input type="text" name="invoice_password" id="invoice_password" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                       value="{{ get_setting('invoice_pre_password') == '' ? '' : get_setting('invoice_pre_password') }}"
                       autocomplete="off">
            </div>

            <div class="mb-4">
                <label for="invoice_group_id">
                    @lang('invoice_group')
                </label>
                <select name="invoice_group_id" id="invoice_group_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select">
                    @foreach($invoice_groups as $invoice_group)
                    <option value="{{ $invoice_group->invoice_group_id }}"
                    @php
                        check_select(get_setting('default_invoice_group'), $invoice_group->invoice_group_id) }}>
                                                {!! $invoice_group->invoice_group_name !!}</option>@endforeach
                </select>
            </div>

        </div>

        <div class="modal-footer">
            <div class="inline-flex rounded-md shadow-sm">
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors" id="quote_to_invoice_confirm" type="button">
                    <i class="fa fa-check"></i> @lang('submit')
                </button>
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> @lang('cancel')
                </button>
            </div>
        </div>

    </form>

</div>
