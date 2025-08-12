@php namespace Modules\Customvalues\Views; @endphp
<div class="table-responsive">

    <table class="table table-hover table-striped">

        <thead>
        <tr>
            <th>@@lang('id')</th>
            <th>@@lang('field')</th>
            <th>@@lang('elements')</th>
            <th>@@lang('table')</th>
            <th>@@lang('position')</th>
            <th>@@lang('type')</th>
            <th>@@lang('options')</th>
        </tr>
        </thead>

        <tbody>
@php foreach ($custom_values as $custom_values) {
    $href = site_url('custom_fields/form/' . $custom_values->custom_field_id);
    $alpha = str_replace('-', '_', mb_strtolower($custom_values->custom_field_type));
    $position = $positions[$custom_values->custom_field_table][$custom_values->custom_field_location];
    @endphp
            <tr>
                <td>{{ anchor($href, $custom_values->custom_field_id, ' title="' . trans('edit') . '"') }}</td>
                <td>{{ anchor($href, '<i class="fa fa-edit fa-margin"></i> ' . htmlsc($custom_values->custom_field_label), ' class="btn btn-sm btn-default"') }}</td>
                <td>{{ $custom_values->count }}</td>
                <td>@php
    _trans($custom_tables[$custom_values->custom_field_table]);
    @endphp</td>
                <td>{{ $position }}</td>
                <td>@php
    _trans($alpha);
    @endphp</td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> @@lang('options')
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ url('custom_values/field/' . $custom_values->custom_field_id) }}">
                                    <i class="fa fa-edit fa-margin"></i> @@lang('edit') (@@lang('values'))
                                </a>
                            </li>
                            <li>
                                <form action="{{ url('custom_fields/delete/' . $custom_values->custom_field_id) }}"
                                      method="POST">
                                    @php
    _csrf_field();
    @endphp
                                    <button type="submit" class="dropdown-button"
                                            onclick="return confirm('@@lang('delete_record_warning')');">
                                        <i class="fa fa-trash-o fa-margin"></i> @@lang('delete')
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
<?php
} @endphp
        </tbody>
    </table>
</div>
<?php 