<div id="headerbar">
    <h1 class="headerbar-title">@lang('invoices')</h1>
    <div class="headerbar-item float-right">
        <a class="create-invoice inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" href="#">
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>
    <div class="headerbar-item float-right">
        {{ pager(site_url('invoices/client/' . $client_id . '/' . $status), 'mdl_invoices') }}
    </div>
    <div class="headerbar-item float-right">
        @php
            $baseClasses = 'inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors';
            $activeClasses = 'bg-brand-primary text-white border-transparent hover:bg-brand-primary-dark focus:ring-brand-primary';
            $inactiveClasses = 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-brand-primary';
        @endphp

        <div class="inline-flex rounded-md shadow-sm index-options">
            <a href="{{ url('invoices/client/' . $client_id . '/open') }}"
               class="{{ $baseClasses }} {{ $status == 'open' ? $activeClasses : $inactiveClasses }}">
                @lang('open')
            </a>
            <a href="{{ url('invoices/client/' . $client_id . '/closed') }}"
               class="{{ $baseClasses }} {{ $status == 'closed' ? $activeClasses : $inactiveClasses }}">
                @lang('closed')
            </a>
            <a href="{{ url('invoices/client/' . $client_id . '/overdue') }}"
               class="{{ $baseClasses }} {{ $status == 'overdue' ? $activeClasses : $inactiveClasses }}">
                @lang('overdue')
            </a>
        </div>
    </div>

</div>

<div id="content" class="table-content">
    @include('invoices/partial_invoice_table', ['invoices' => $invoices])
</div>
