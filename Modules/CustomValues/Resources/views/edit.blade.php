@php namespace Modules\CustomValues\Views;

$href = site_url('custom_fields/form/' . $value->custom_field_id);
$link = anchor($href, '<i class="fa fa-edit fa-margin"></i> ' . htmlsc($value->custom_field_label), ' class="btn btn-sm btn-default"');
$alpha = strtr(mb_strtolower($value->custom_field_type), ['-' => '_']);
$table = strtr($value->custom_field_table, ['ip_' => '', '_custom' => '']); @endphp
<form method="post">

    @csrf

    <div id="headerbar">
        <h1 class="headerbar-title">@lang('custom_values_edit')</h1>
        @include('layout.header_buttons')
        <div class="headerbar-item pull-right">
            <a href="{{ url('custom_values/field/' . $value->custom_field_id) }}" class="btn btn-sm btn-default">
                <i class="fa fa-eye fa-margin"></i> @lang('values')</a>
        </div>
        <div class="visible-sm visible-md visible-lg headerbar-item pull-right">
            <div class="badge">@lang('table'): @php _trans($table); @endphp</div>
            <div class="badge">@lang('position'): {{ $position }}</div>
            <div class="badge">@lang('type'): @php _trans($alpha); @endphp</div>
            @lang('field'): {{ $link }}
        </div>
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">

                @include('layout.alerts')

                <div class="form-group">
                    <label for="custom_values_value">@lang('label'):</label>
                    <input type="text" name="custom_values_value" id="custom_values_value" class="form-control"
                           value="{!! $value->custom_values_value !!}" required>
                </div>
                <hr>

                <div class="row visible-xs">
                    <div class="col-xs-12">
                        <div class="form-group">@lang('field'): {{ $link }}</div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group badge">@lang('table'): @php _trans($table); @endphp</div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group badge">@lang('position'): {{ $position }}</div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group badge">@lang('type'): @php _trans($alpha); @endphp</div>
                    </div>
                </div>

            </div>

            @include('layout/partial/custom_field_usage_list', ['custom_field_table' => $value->custom_field_table])

        </div>
    </div>

</form>
