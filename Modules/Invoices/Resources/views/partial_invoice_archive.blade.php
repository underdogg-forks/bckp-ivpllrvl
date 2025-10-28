
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

        <thead>
        <tr>
            <th>@lang('invoice')</th>
            <th>@lang('created')</th>
        </tr>
        </thead>

        <tbody>
        @foreach($invoices_archive as $invoice)
        <tr>
            <td>
                <a href="{{ url('invoices/download/' . basename($invoice)) }}"
                   title="@lang('invoice')">
                    {{ basename($invoice) }}
                </a>
            </td>

            <td>
                {{ date('F d Y H:i:s.', filemtime($invoice)) }}
            </td>

        </tr>@endforeach
        </tbody>

    </table>
</div>
