
<div id="headerbar">
    <h1 class="headerbar-title">@lang('units')</h1>

    <div class="headerbar-item float-right">
        <a class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" href="{{ url('units/form') " }}>
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>

    <div class="headerbar-item float-right">
        {{ pager(site_url('units/index'), 'mdl_units') }}
    </div>

</div>

<div id="content" class="table-content">

    @include('layout.alerts')

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

            <thead>
            <tr>
                <th>@lang('unit_name')</th>
                <th>@lang('unit_name_plrl')</th>
                <th>@lang('options')</th>
            </tr>
            </thead>

            <tbody>
            @foreach($units as $unit)
            <tr>
                <td>{!! $unit->unit_name !!}</td>
                <td>{!! $unit->unit_name_plrl !!}</td>
                <td>
                    <div class="options inline-flex rounded-md shadow-sm">
                        <a class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5"
                           data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> @lang('options')
                        </a>
                        <ul class="absolute z-10 mt-2 min-w-[160px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg overflow-hidden">
                            <li>
                                <a href="{{ url('units/form/' . $unit->unit_id) " }}>
                                    <i class="fa fa-edit fa-margin"></i> @lang('edit')
                                </a>
                            </li>
                            <li>
                                <form action="{{ url('units/delete/' . $unit->unit_id) }}"
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

</div>
