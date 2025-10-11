<div id="headerbar">
    <h1 class="headerbar-title">@lang('invoice_groups')</h1>

    <div class="headerbar-item float-right">
        <a class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" href="{{ url('invoice_groups/form') " }}>
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>

    <div class="headerbar-item float-right">
        {{ pager(site_url('invoice_groups/index'), 'mdl_invoice_groups') }}
    </div>

</div>

<div id="content" class="table-content">

    @include('layout.alerts')

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

            <thead>
            <tr>
                <th>@lang('name')</th>
                <th>@lang('next_id')</th>
                <th>@lang('left_pad')</th>
                <th>@lang('options')</th>
            </tr>
            </thead>

            <tbody>
            @foreach($invoice_groups as $invoice_group)
                <tr>
                    <td>{!! $invoice_group->invoice_group_name !!}</td>
                    <td>{{ $invoice_group->invoice_group_next_id }}</td>
                    <td>{{ $invoice_group->invoice_group_left_pad }}</td>
                    <td>
                        <div class="options inline-flex rounded-md shadow-sm">
                            <a class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> @lang('options')
                            </a>
                            <ul class="absolute z-10 mt-2 min-w-[160px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg overflow-hidden">
                                <li>
                                    <a href="{{ url('invoice_groups/form/' . $invoice_group->invoice_group_id) " }}>
                                        <i class="fa fa-edit fa-margin"></i> @lang('edit')
                                    </a>
                                </li>
                                <li>
                                    <form
                                        action="{{ url('invoice_groups/delete/' . $invoice_group->invoice_group_id) }}"
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
</div>
