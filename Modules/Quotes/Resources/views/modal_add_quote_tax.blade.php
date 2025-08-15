@php namespace Modules\Quotes\Views; @endphp
<script>
    $(function () {
        $('#quote_tax_submit').click(function () {
            var tax_rate_id = $('#tax_rate_id').val();
            if ('0' == tax_rate_id) return;
            show_loader(); // Show spinner
            $.post("{{ url('quotes/ajax/save_quote_tax_rate');
?>", {
                    quote_id: {{ $quote_id }},
                    tax_rate_id: tax_rate_id,
                    include_item_tax: $('#include_item_tax').val()
                },
                function (data) {
                    var response = json_parse(data, {{ (int) IP_DEBUG }});
                    if (response.success === 1) {
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
            <h4 class="panel-title">@lang('add_quote_tax')</h4>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <label for="tax_rate_id">
                    @lang('tax_rate')
                </label>

                <div class="controls">
                    <select name="tax_rate_id" id="tax_rate_id" class="form-control simple-select" required>
                        <option value="0">@lang('none')</option>
                        @foreach($tax_rates as $tax_rate)
                            <option value="{{ $tax_rate->tax_rate_id }}">
                                {{ format_amount($tax_rate->tax_rate_percent) . '% - ' . htmlsc($tax_rate->tax_rate_name) }}
                            </option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="include_item_tax">
                    @lang('tax_rate_placement') }}
                </label>

                <div class="controls">
                    <select name="include_item_tax" id="include_item_tax" class="form-control simple-select" required>
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
            <div class="btn-group">
                <button class="btn btn-success" id="quote_tax_submit" type="button">
                    <i class="fa fa-check"></i> @lang('submit')
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> @lang('cancel')
                </button>
            </div>
        </div>

    </form>

</div>
<?php
