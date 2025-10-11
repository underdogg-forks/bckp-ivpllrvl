
<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700">

    <thead>
    <tr>
        <th>@lang('id')</th>
        <th>@lang('label')</th>
        <th>@lang('options')</th>
    </tr>
    </thead>

    <tbody>
    @foreach($elements as $element)
    <tr>
        <td>{{ $element->custom_values_id }}</td>
        <td>{!! $element->custom_values_value !!}</td>
        <td>
            <div class="options inline-flex rounded-md shadow-sm">
                <a class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5" data-toggle="dropdown"
                   href="#">
                    <i class="fa fa-cog"></i> @lang('options')
                </a>
                <ul class="absolute z-10 mt-2 min-w-[160px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg overflow-hidden">
                    <li>
                        <a href="{{ url('custom_values/edit/' . $element->custom_values_id) " }}>
                            <i class="fa fa-edit fa-margin"></i> @lang('edit')
                        </a>
                    </li>
                    <li>
                        <form action="{{ url('custom_values/delete/' . $element->custom_values_id) }}"
                              method="POST">
                            @csrf
                            <input type="hidden" name="custom_field_id" value="{{ $id " }}>
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                    onclick="return confirm(`@lang('delete_record_warning')`);">
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

