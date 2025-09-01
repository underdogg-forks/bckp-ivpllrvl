@php
    $href = url('custom_fields/form/' . $field->custom_field_id);
    $link = '<a href="' . $href . '" class="btn btn-default"><i class="fa fa-edit fa-margin"></i> ' . e($field->custom_field_label) . '</a>';
    $alpha = strtr(mb_strtolower($field->custom_field_type), ['-' => '_']);
    $table = strtr($field->custom_field_table, ['ip_' => '', '_custom' => '']);
@endphp
<form method="post">
    @csrf
    <div id="headerbar">
        <h1 class="headerbar-title">@lang('custom_values_new')</h1>
        @include('layout.header_buttons')
        <div class="visible-sm visible-md visible-lg headerbar-item pull-right">
            <div class="badge">@lang('table'): {{ $table }}</div>
            <div class="badge">@lang('position'): {{ $position }}</div>
            <div class="badge">@lang('type'): {{ $alpha }}</div>
            @lang('field'): {!! $link !!}
        </div>
    </div>
    <div id="content">
        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">
                @include('layout.alerts')
                <div class="form-group">
                    <label for="custom_values_value">@lang('value'):</label>
                    <input type="text" class="form-control" name="custom_values_value" id="custom_values_value"
                           required>
                </div>
                <div class="row visible-xs">
                    <div class="col-xs-12">
                        <div class="form-group">@lang('field'): {!! $link !!}</div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group badge">@lang('table'): {{ $table }}</div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group badge">@lang('position'): {{ $position }}</div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group badge">@lang('type'): {{ $alpha }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
