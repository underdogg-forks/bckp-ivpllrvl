<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th>@lang('id')</th>
            <th>@lang('field')</th>
            <th>@lang('elements')</th>
            <th>@lang('table')</th>
            <th>@lang('position')</th>
            <th>@lang('type')</th>
            <th>@lang('options')</th>
        </tr>
        </thead>
        <tbody>
        @foreach($custom_values as $custom_value)
            @php
                $href = url('custom_fields/form/' . $custom_value->custom_field_id);
                $alpha = str_replace('-', '_', mb_strtolower($custom_value->custom_field_type));
                $position = $positions[$custom_value->custom_field_table][$custom_value->custom_field_location] ?? '';
            @endphp
            <tr>
                <td><a href="{{ $href }}" title="@lang('edit')">{{ $custom_value->custom_field_id }}</a></td>
                <td><a href="{{ $href }}" class="btn btn-sm btn-default"><i
                            class="fa fa-edit fa-margin"></i> {{ $custom_value->custom_field_label }}</a></td>
                <td>{{ $custom_value->count }}</td>
                <td>{{ $custom_tables[$custom_value->custom_field_table] ?? $custom_value->custom_field_table }}</td>
                <td>{{ $position }}</td>
                <td>{{ $alpha }}</td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> @lang('options')
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ url('custom_values/field/' . $custom_value->custom_field_id) }}">
                                    <i class="fa fa-edit fa-margin"></i> @lang('edit') (@lang('values'))
                                </a>
                            </li>
                            <li>
                                <form action="{{ url('custom_fields/delete/' . $custom_value->custom_field_id) }}"
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
        @endforeach
        </tbody>
    </table>
</div>
