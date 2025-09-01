
<div id="headerbar">

    <h1 class="headerbar-title">@lang('invoices')</h1>

    <div class="headerbar-item pull-right">
        <a class="create-invoice btn btn-sm btn-primary" href="#">
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('invoices/client/' . $client_id . '/' . $status), 'mdl_invoices') }}
    </div>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm index-options">
            <a href="{{ url('invoices/client/' . $client_id . '/open') }}"
               class="btn {{ $status == 'open' ? 'btn-primary' : 'btn-default' }}">
                @lang('open')
            </a>
            <a href="{{ url('invoices/client/' . $client_id . '/closed') }}"
               class="btn  {{ $status == 'closed' ? 'btn-primary' : 'btn-default' }}">
                @lang('closed')
            </a>
            <a href="{{ url('invoices/client/' . $client_id . '/overdue') }}"
               class="btn  {{ $status == 'overdue' ? 'btn-primary' : 'btn-default' }}">
                @lang('overdue')
            </a>
        </div>
    </div>

</div>

<div id="content" class="table-content">

    @include('invoices/partial_invoice_table', ['invoices' => $invoices])

</div>
