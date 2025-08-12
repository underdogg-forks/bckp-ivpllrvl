@php namespace Modules\CustomValues\Views;

$href = site_url('custom_fields/form/' . $field->custom_field_id);
$link = anchor($href, '<i class="fa fa-edit fa-margin"></i> ' . htmlsc($field->custom_field_label), ' class="btn btn-sm btn-default"');
$alpha = strtr(mb_strtolower($field->custom_field_type), ['-' => '_']);
$table = strtr($field->custom_field_table, ['ip_' => '', '_custom' => '']); @endphp

<div id="headerbar">
    <h1 class="headerbar-title">@lang('custom_values')</h1>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
            <a class="btn btn-default" href="{{ url('custom_values') }}">
                <i class="fa fa-arrow-left"></i> @lang('back')
            </a>
            <a class="btn btn-primary" href="{{ url('custom_values/create/' . $id) }}">
                <i class="fa fa-plus"></i> @lang('new')
            </a>
        </div>
    </div>
    <div class="visible-sm visible-md visible-lg headerbar-item pull-right">
        <div class="badge">@lang('table'): @php _trans($table); @endphp</div>
        <div class="badge">@lang('position'): {{ $position }}</div>
        <div class="badge">@lang('type'): @php _trans($alpha); @endphp</div>
        @lang('field'): {{ $link }}
    </div>
</div>

<div id="content">
    @include('layout/alerts')

    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">

            <div class="form-group">
                <div id="filter_results">
                    @include('custom_values/partial_custom_values_field')
                </div>
            </div>

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

        @include('layout/partial/custom_field_usage_list', ['custom_field_table' => $field->custom_field_table])

    </div>
</div>
