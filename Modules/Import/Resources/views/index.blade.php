
<div id="headerbar">
    <h1 class="headerbar-title">@lang('import_data')</h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="{{ url('import/form') }}">
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('import/index'), 'mdl_import') }}
    </div>

</div>

<div id="content" class="table-content">

    {{ $this->layout->loadView('layout/alerts') }}

    <div class="table-responsive">
        <table class="table table-striped">

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
                    <div class="options btn-group btn-group-sm">
                        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> @lang('options')
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <form action="{{ url('import/delete/' . $import->import_id) }}"
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
                @endif
            </tbody>

        </table>
    </div>

</div>
<?php
