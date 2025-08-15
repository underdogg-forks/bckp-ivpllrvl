
<div class="table-responsive">
    <table class="table table-hover table-striped">

        <thead>
        <tr>
            <th>@lang('label')</th>
            <th>@lang('table')</th>
            <th>@lang('position')</th>
            <th>@lang('type')</th>
            <th>@lang('order')</th>
            <th>@lang('options')</th>
        </tr>
        </thead>

        <tbody>
        @foreach($custom_fields as $custom_field) {
    $alpha = str_replace('-', '_', mb_strtolower($custom_field->custom_field_type));
    $position = $positions[$custom_field->custom_field_table][$custom_field->custom_field_location];

        <tr>
            <td>{!! $custom_field->custom_field_label !!}</td>
            <td>@php
                    _trans($custom_tables[$custom_field->custom_field_table])</td>
            <td>{{ $position }}</td>
            <td>@php
                    _trans($alpha);
                </td>
            <td>{{ $custom_field->custom_field_order }}</td>
            <td>
                <div class="options btn-group btn-group-sm">
                    <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-cog"></i> @lang('options')
                    </a>
                    @if(in_array($custom_field->custom_field_type, $custom_value_fields))
                    <a href="{{ url('custom_values/field/' . $custom_field->custom_field_id) }}"
                       class="btn btn-default">
                        <i class="fa fa-list fa-margin"></i> @lang('values')
                    </a>@endforeach
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ url('custom_fields/form/' . $custom_field->custom_field_id) }}">
                                <i class="fa fa-edit fa-margin"></i> @lang('edit')
                            </a>
                        </li>
                        <li>
                            <form action="{{ url('custom_fields/delete/' . $custom_field->custom_field_id) }}"
                                  method="POST">
                                @csrf
                                <button type="submit" class="dropdown-button"
                                        onclick="return confirm('@lang('delete_record_warning')');">
                                    <i class="fa fa-trash-o fa-margin"></i> @lang('delete')
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
            @endif
        </tbody>

    </table>

</div>
<?php
