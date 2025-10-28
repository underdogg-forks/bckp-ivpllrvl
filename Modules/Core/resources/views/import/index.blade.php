
<div id="headerbar">
    <h1 class="headerbar-title">@lang('import_data')</h1>

    <div class="headerbar-item float-right">
        <a class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" href="{{ url('import/form') " }}>
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>

    <div class="headerbar-item float-right">
        {{ pager(site_url('import/index'), 'mdl_import') }}
    </div>

</div>

<div id="content" class="table-content">

    {{ $this->layout->loadView('layout/alerts') }}

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

            <thead>
            <tr>
                <th>@lang('id')</th>
                <th>@lang('date')</th>
                <th>@lang('clients')</th>
                <th>@lang('invoices')</th>
                <th>@lang('invoice_items')</th>
                <th>@lang('payments')</th>
                <th>@lang('options')</th>
            </tr>
            </thead>

            <tbody>
            @foreach($imports as $import)
            <tr>
                <td>{{ $import->import_id }}</td>
                <td>{{ $import->import_date }}</td>
                <td>{{ $import->num_clients }}</td>
                <td>{{ $import->num_invoices }}</td>
                <td>{{ $import->num_invoice_items }}</td>
                <td>{{ $import->num_payments }}</td>
                <td>
                    <div class="options inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm">
                        <a class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> @lang('options')
                        </a>
                        <ul class="absolute z-10 mt-2 min-w-[160px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg overflow-hidden">
                            <li>
                                <form action="{{ url('import/delete/' . $import->import_id) }}"
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
