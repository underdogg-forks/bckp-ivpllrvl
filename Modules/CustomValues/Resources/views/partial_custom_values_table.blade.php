<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
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
                <td><a href="{{ $href }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"><i
                            class="fa fa-edit fa-margin"></i> {{ $custom_value->custom_field_label }}</a></td>
                <td>{{ $custom_value->count }}</td>
                <td>{{ $custom_tables[$custom_value->custom_field_table] ?? $custom_value->custom_field_table }}</td>
                <td>{{ $position }}</td>
                <td>{{ $alpha }}</td>
                <td>
                    <div class="options inline-flex rounded-md shadow-sm">
                        <a class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> @lang('options')
                        </a>
                        <ul class="absolute z-10 mt-2 min-w-[160px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg overflow-hidden">
                            <li>
                                <a href="{{ url('custom_values/field/' . $custom_value->custom_field_id) " }}>
                                    <i class="fa fa-edit fa-margin"></i> @lang('edit') (@lang('values'))
                                </a>
                            </li>
                            <li>
                                <form action="{{ url('custom_fields/delete/' . $custom_value->custom_field_id) }}"
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
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
