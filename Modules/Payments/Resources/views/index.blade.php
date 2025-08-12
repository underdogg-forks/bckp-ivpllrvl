@php namespace Modules\Payments\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@lang('payments')</h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="{{ url('payments/form') }}">
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('payments/index'), 'mdl_payments') }}
    </div>

</div>

<div id="content" class="table-content">

    @include('layout/alerts')

    <div id="filter_results">
        @include('payments/partial_payments_table')
    </div>

</div>
