
<script>
    $(function () {
        // Display the create quote modal
        $('#modal_create_recurring').modal('show');

        get_recur_start_date();

        $('#recur_frequency').change(function () {
            get_recur_start_date();
        });

        // Select2 for all select inputs
        $('.simple-select').select2();

        // Creates the invoice
        $('#create_recurring_confirm').click(function () {
                show_loader(); // Show spinner
                $.post("{{ url('invoices/ajax/create_recurring');
?>", {
                    invoice_id: {{ $invoice_id }},
                recur_start_date: $('#recur_start_date').val(),
                    recur_end_date
            :
                $('#recur_end_date').val(),
                    recur_frequency
            :
                $('#recur_frequency').val()
            },
            function (data) {
                var response = json_parse(data, {{ (int) IP_DEBUG }});
                if (response.success === 1) {
                    window.location = "{{ url('invoices/view') }}/{{ $invoice_id }}";
                } else {
                    // The validation was not successful
                    close_loader();
                    $('.control-group').removeClass('has-error');
                    for (var key in response.validation_errors) {
                        $('#' + key).parent().parent().addClass('has-error');
                    }
                }
            });
    });

    function get_recur_start_date() {
        $.post("{{ url('invoices/ajax/get_recur_start_date') }}", {
                invoice_date: $('#invoice_date_created').val(),
                recur_frequency: $('#recur_frequency').val()
            },
            function (data) {
                $('#recur_start_date').val(data);
            });
    }
    })
    ;
</script>

<div id="modal_create_recurring" class="modal modal-lg"
     role="dialog" aria-labelledby="modal_create_recurring" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title">@lang('create_recurring')</h4>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <label for="recur_frequency">@lang('every')</label>
                <select name="recur_frequency" id="recur_frequency" class="form-control simple-select">
                    @foreach($recur_frequencies as $key => $lang)
                    <option value="{{ $key }}">
                    @php
                        _trans($lang) }}
                                            </option>@endforeach
                </select>
            </div>

            <div class="form-group has-feedback">
                <label for="recur_start_date">@lang('start_date')</label>
                <div class="input-group">
                    <input name="recur_start_date" id="recur_start_date"
                           class="form-control datepicker">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar fa-fw"></i>
                    </span>
                </div>
            </div>

            <div class="form-group has-feedback">
                <label for="recur_end_date">@lang('end_date') ({{ trans('optional') }})</label>

                <div class="input-group">
                    <input name="recur_end_date" id="recur_end_date"
                           class="form-control datepicker">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar fa-fw"></i>
                    </span>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success" id="create_recurring_confirm" type="button">
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
