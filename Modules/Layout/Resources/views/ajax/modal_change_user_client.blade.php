@php
    // Determine for who is (Replace $) (type) & (who) _id system
    $who = empty($user_id) ? 'client' : 'user';
    // Basic test
    $type = empty($quote_id) ? 'invoice' : 'quote';
    // Basic test
    $who_id = $who . '_id';
    // Add *_id user/client
    $type_id = $type . '_id';
    // Add *_id quote/invoice
    $type_id = $this->input->post($type_id) ?: false;
    // Type exist? get id
    if (!$type_id) {
        return;
        // No quote/invoice id do nothing
    }
    $permissive = get_setting('enable_permissive_search_' . $who . 's');
@endphp
<script>
    $(function () {
        // Display user change for quote or invoice modal
        $('#change-{{ $who }}').modal('show');

        @include($who . 's/script_select2_' . $who . '_id.js')

        // Change the user or client
        $('#{{ $who }}_change_confirm').click(function () {
            // Show loader
            show_loader();

            // Posts the data to validate
            $.post("{{ url($type . 's/ajax/change_' . $who) }}", {
                    {{ $who }}_id: $('#change_{{ $who }}_id').val(),
                    {{ $type }}_id: $('#{{ $type }}_id').val()
                },
                function (data) {
                    var response = json_parse(data, {{ (int) IP_DEBUG }});
                    @if(response.success === 1) {
                        // The validation was successful and quote/invoice was Updated
                        window.location = "{{ url($type . 's/view') }}/" + response.{{ $type }}_id;
                    } else {
                        // The validation was not successful
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

<div id="change-{{ $who }}" class="modal modal-lg" role="dialog" aria-labelledby="modal_change_{{ $who }}"
     aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">@trans("change_$who")</h4>
        </div>
        <div class="modal-body">
            <div class="mb-4 has-feedback">
                <label for="change_{{ $who }}_id">@trans($who)</label>
                <div class="input-group">
                    <span id="toggle_permissive_search_{{ $who }}s" class="input-group-addon"
                          title="@trans('enable_permissive_search_' . $who . 's')"
                          style="cursor:pointer;">
                        <i class="fa fa-toggle-{{ $permissive ? 'on' : 'off' }} fa-fw"></i>
                    </span>
                    <select name="{{ $who }}_id" id="change_{{ $who }}_id" class="{{ $who }}-id-select w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                            autofocus="autofocus" required>
                        @php
                            $who_id_val = ${$who}->{$who_id} ?? $this->input->post($who_id);
                            if ($who_id_val) {
                                $format = 'format_' . $who;
                                $name_prop = $who . '_name';
                                $name = empty(${$who}->{$name_prop}) ? $format($who_id_val) : ${$who}->{$name_prop};
                        @endphp
                                <option value="{{ $who_id_val }}">{!! $name !!}</option>
                        @php
                            }
                        @endphp
                    </select>
                </div>
            </div>
            <input class="hidden" id="{{ $type }}_id" value="{{ $type_id }}">
            <input class="hidden" id="input_permissive_search_{{ $who }}s" value="{{ $permissive }}">
        </div>
        <div class="modal-footer">
            <div class="inline-flex rounded-md shadow-sm">
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors ajax_loader" id="{{ $who }}_change_confirm" type="button">
                    <i class="fa fa-check"></i> @trans('submit')
                </button>
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> @trans('cancel')
                </button>
            </div>
        </div>
    </form>
</div>
