@php
    $disabled = $custom_field_usage ? ' disabled' : '';
    $custom_field_table = $this->mdl_custom_fields->form_value('custom_field_table');
    $custom_field_type = $this->mdl_custom_fields->form_value('custom_field_type');

<form method="post">
    @csrf

    <div id="headerbar">
        <h1 class="headerbar-title">{{ trans('custom_field_form') }}</h1>
        @include('layout.header_buttons')
        <div class="headerbar-item float-right">
            <a href="{{ url('custom_values/field/' . $custom_field_id) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fa fa-list fa-margin"></i> {{ trans('values') }}
            </a>
        </div>
    </div>
    @if ($disabled)
        <input type="hidden" name="custom_field_table" value="{{ $custom_field_table " }}>
        <input type="hidden" name="custom_field_type" value="{{ $custom_field_type " }}>
    @endif
    <div id="content" class="flex flex-wrap -mx-4">
        <div class="w-full px-4 md:w-1/2 col-md-offset-3">
            @include('layout.alerts')

            <div class="mb-4">
                <label for="custom_field_label">{{ trans('label') }}</label>
                <input type="text" name="custom_field_label" id="custom_field_label" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                       value="{{ $this->mdl_custom_fields->form_value('custom_field_label', true) }}" required>
            </div>

            <div class="mb-4">
                <label for="custom_field_table">{{ trans('table') }}</label>
                <select name="custom_field_table" id="custom_field_table"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select"{{ $disabled ?: ' required' }}>
                    @php
                        // New field? Auto select (work if come from custom_fields/table/*)
                        $custom_field_table = $custom_field_table ?: (isset($_SERVER['HTTP_REFERER']) ? 'ip_' . basename($_SERVER['HTTP_REFERER']) . '_custom' : '');

                    @foreach ($custom_field_tables as $table => $label)
                        <option value="{{ $table }}" @php check_select($custom_field_table, $table); >
                            {{ trans($label) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="custom_field_location">{{ trans('position') }}</label>
                <select name="custom_field_location" id="custom_field_location"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select"></select>
            </div>

            <div class="mb-4">
                <label for="custom_field_type">{{ trans('type') }}</label>
                <select name="custom_field_type" id="custom_field_type"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select"{{ $disabled ?: ' required' }}>
                    @foreach ($custom_field_types as $type)
                        @php
                            $alpha = str_replace('-', '_', mb_strtolower($type));

                        <option value="{{ $type }}" @php check_select($custom_field_type, $type); >
                            {{ trans($alpha) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="custom_field_order">{{ trans('order') }}</label>
                <input type="number" name="custom_field_order" id="custom_field_order" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                       value="{{ $this->mdl_custom_fields->form_value('custom_field_order', true) " }}>
            </div>
        </div>

        @include('layout/partial/custom_field_usage_list', ['custom_field_table' => $custom_field_table])
    </div>
</form>
<script>
    $(function () {
        function updatePositions(index, selKey) {
            $("#custom_field_location option").remove();
            var key = Object.keys(jsonPositions)[index];
            for (var pos in jsonPositions[key]) {
                var opt = $("<option>");
                opt.attr("value", pos);
                opt.text(jsonPositions[key][pos]);
                @if(selKey == pos) {
                    opt.attr("selected", "selected");
                }
                $("#custom_field_location").append(opt);
            }
        }

        $("#custom_field_table").on("change", function () {
            var optionIndex = $("#custom_field_table option:selected").index();
            updatePositions(optionIndex);
        });

        var jsonPositions = json_parse('{{ json_encode($positions) }}', {{ (int) IP_DEBUG }});
        var optionIndex = $("#custom_field_table option:selected").index();
        // Init Selector with Selected value
        updatePositions(optionIndex, {{ $custom_field_location }});
    });
</script>
