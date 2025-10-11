
<script>
    $(function () {
        $('#modal-create-credit-invoice').modal('show');
        $('#create-credit-confirm').click(function () {
            show_loader(); // Show spinner
            $.post("{{ site_url('invoices/ajax/create_credit');
?>", {
                    invoice_id: {{ $invoice_id }},
                    client_id: $('#client_id').val(),
                    invoice_date_created: $('#invoice_date_created').val(),
                    invoice_group_id: $('#invoice_group_id').val(),
                    invoice_time_created: '{{ date('H:i:s') }}',
                    invoice_password: $('#invoice_password').val(),
                    user_id: $('#user_id').val()
                },
                function (data) {
                    var response = json_parse(data, {{ (int) IP_DEBUG }});
                    @if(response.success === 1) {
                        window.location = "{{ url('invoices/view') }}/" + response.invoice_id;
                    }
                    @else
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
    });
</script>

<div id="modal-create-credit-invoice" class="modal modal-lg" role="dialog" aria-labelledby="modal-create-credit-invoice"
     aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">@lang('create_credit_invoice')</h4>
        </div>
        <div class="modal-body">

            <input type="hidden" name="user_id" id="user_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                   value="{{ $invoice->user_id " }}>

            <input type="hidden" name="parent_id" id="parent_id"
                   value="{{ $invoice->invoice_id " }}>

            <input type="hidden" name="client_id" id="client_id" class="hidden"
                   value="{{ $invoice->client_id " }}>

            <input type="hidden" name="invoice_date_created" id="invoice_date_created"
                   value="@php $credit_date = date_from_mysql(date('Y-m-d', time()), true);
echo $credit_date; ">

            <div class="mb-4">
                <label for="invoice_password">@lang('invoice_password')</label>
                <input type="text" name="invoice_password" id="invoice_password" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                       value="{{ get_setting('invoice_pre_password') == '' ? '' : get_setting('invoice_pre_password') }}"
                       style="margin: 0 auto;" autocomplete="off">
            </div>

            <div>
                <select name="invoice_group_id" id="invoice_group_id" class="hidden">
                    @foreach($invoice_groups as $invoice_group)
                        <option value="{{ $invoice_group->invoice_group_id }}"
                            @if(get_setting('default_invoice_group') == $invoice_group->invoice_group_id)
{selected="selected";
        $credit_invoice_group = htmlsc($invoice_group->invoice_group_name)}@endforeach
    >
                            {{ $credit_invoice_group }}
                        </option>@endforeach
                </select>
            </div>

            <p><strong>@lang('credit_invoice_details')</strong></p>

            <ul>
                <li>{{ __('client') . ': ' . htmlsc($invoice->client_name) }}</li>
                <li>{{ trans('credit_invoice_date') . ': ' . $credit_date }}</li>
                <li>{{ trans('invoice_group') . ': ' . $credit_invoice_group }}</li>
            </ul>

            <div class="p-4 mb-4 text-red-700 dark:text-red-200 bg-red-100 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg no-margin">
                @lang('create_credit_invoice_alert')
            </div>

        </div>

        <div class="modal-footer">
            <div class="inline-flex rounded-md shadow-sm">
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors" id="create-credit-confirm" type="button">
                    <i class="fa fa-check"></i> @lang('confirm')
                </button>
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> @lang('cancel')
                </button>
            </div>
        </div>

    </form>

</div>
