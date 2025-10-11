@php
    $disabled = $custom_field_usage ? ' disabled' : '';
    $custom_field_table = $this->mdl_custom_fields->form_value('custom_field_table');
    $custom_field_type = $this->mdl_custom_fields->form_value('custom_field_type');

<form method="post">
    @csrf

    <div id="headerbar">
        <h1 class="headerbar-title">{{ __('custom_field_form') }}</h1>
        @include('layout.header_buttons')
        <div class="headerbar-item pull-right">
            <a href="{{ url('custom_values/field/' . $custom_field_id) }}" class="btn btn-sm btn-default">
                <i class="fa fa-list fa-margin"></i> {{ __('values') }}
            </a>
        </div>
    </div>
    @if ($disabled)
        <input type="hidden" name="custom_field_table" value="{{ $custom_field_table " }}>
        <input type="hidden" name="custom_field_type" value="{{ $custom_field_type " }}>
    @endif
    <div id="content" class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">
            @include('layout.alerts')

            <div class="form-group">
                <label for="custom_field_label">{{ __('label') }}</label>
                <input type="text" name="custom_field_label" id="custom_field_label" class="form-control"
                       value="{{ $this->mdl_custom_fields->form_value('custom_field_label', true) }}" required>
            </div>

            <div class="form-group">
                <label for="custom_field_table">{{ __('table') }}</label>
                <select name="custom_field_table" id="custom_field_table"
                        class="form-control simple-select"{{ $disabled ?: ' required' }}>
                    @php
                        // New field? Auto select (work if come from custom_fields/table/*)
                        $custom_field_table = $custom_field_table ?: (isset($_SERVER['HTTP_REFERER']) ? 'ip_' . basename($_SERVER['HTTP_REFERER']) . '_custom' : '');

                    @foreach ($custom_field_tables as $table => $label)
                        <option value="{{ $table }}" @php check_select($custom_field_table, $table); >
                            {{ __($label) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="custom_field_location">{{ __('position') }}</label>
                <select name="custom_field_location" id="custom_field_location"
                        class="form-control simple-select"></select>
            </div>

            <div class="form-group">
                <label for="custom_field_type">{{ __('type') }}</label>
                <select name="custom_field_type" id="custom_field_type"
                        class="form-control simple-select"{{ $disabled ?: ' required' }}>
                    @foreach ($custom_field_types as $type)
                        @php
                            $alpha = str_replace('-', '_', mb_strtolower($type));

                        <option value="{{ $type }}" @php check_select($custom_field_type, $type); >
                            {{ __($alpha) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="custom_field_order">{{ __('order') }}</label>
                <input type="number" name="custom_field_order" id="custom_field_order" class="form-control"
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
