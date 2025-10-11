
<script>
    $(function () {
        $('#quote_tax_submit').click(function () {
            var tax_rate_id = $('#tax_rate_id').val();
            @if('0' == tax_rate_id) return;
            show_loader(); // Show spinner
            $.post("{{ url('quotes/ajax/save_quote_tax_rate');
?>", {
                    quote_id: {{ $quote_id }},
                    tax_rate_id: tax_rate_id,
                    include_item_tax: $('#include_item_tax').val()
                },
                function (data) {
                    var response = json_parse(data, {{ (int) IP_DEBUG }});
                    @if(response.success === 1) {
                        window.location = "{{ url('quotes/view') }}/" + {{ $quote_id }};
                    }
                    // close_loader(); No error returned (show go to wiki if not success after 10s)  Todo: else // The validation was not successful
                }
            );
        });
    });
</script>

<div id="add-quote-tax" class="modal modal-lg" role="dialog" aria-labelledby="modal_add_quote_tax" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">@lang('add_quote_tax')</h4>
        </div>
        <div class="modal-body">

            <div class="mb-4">
                <label for="tax_rate_id">
                    @lang('tax_rate')
                </label>

                <div class="controls">
                    <select name="tax_rate_id" id="tax_rate_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select" required>
                        <option value="0">@lang('none')</option>
                        @foreach($tax_rates as $tax_rate)
                            <option value="{{ $tax_rate->tax_rate_id " }}>
                                {{ format_amount($tax_rate->tax_rate_percent) . '% - ' . htmlsc($tax_rate->tax_rate_name) }}
                            </option>@endforeach
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="include_item_tax">
                    @lang('tax_rate_placement')
                </label>

                <div class="controls">
                    <select name="include_item_tax" id="include_item_tax" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select" required>
                        <option value="0">
                            @lang('apply_before_item_tax')
                        </option>
                        <option value="1">
                            @lang('apply_after_item_tax')
                        </option>
                    </select>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <div class="inline-flex rounded-md shadow-sm">
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors" id="quote_tax_submit" type="button">
                    <i class="fa fa-check"></i> @lang('submit')
                </button>
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> @lang('cancel')
                </button>
            </div>
        </div>

    </form>

</div>
