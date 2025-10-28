
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

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
            <td>{{ _trans($alpha) }}</td>
            <td>{{ $custom_field->custom_field_order }}</td>
            <td>
                <div class="options inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm">
                    <a class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" data-toggle="dropdown" href="#">
                        <i class="fa fa-cog"></i> @lang('options')
                    </a>
                    @if(in_array($custom_field->custom_field_type, $custom_value_fields))
                    <a href="{{ url('custom_values/field/' . $custom_field->custom_field_id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fa fa-list fa-margin"></i> @lang('values')
                    </a>@endforeach
                    <ul class="absolute z-10 mt-2 min-w-[160px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg overflow-hidden">
                        <li>
                            <a href="{{ url('custom_fields/form/' . $custom_field->custom_field_id) " }}>
                                <i class="fa fa-edit fa-margin"></i> @lang('edit')
                            </a>
                        </li>
                        <li>
                            <form action="{{ url('custom_fields/delete/' . $custom_field->custom_field_id) }}"
                                  method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                        onclick="return confirm('@lang('delete_record_warning')');">
                                    <i class="fa fa-trash-o fa-margin"></i> @lang('delete')
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>@endforeach
        </tbody>

    </table>

</div>
