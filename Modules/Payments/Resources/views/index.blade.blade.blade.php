@php namespace Modules\Payments\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@@lang('payments')</h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="{{ url('payments/form') }}">
            <i class="fa fa-plus"></i> @@lang('new')
        </a>
    </div>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('payments/index'), 'mdl_payments') }}
    </div>

</div>

<div id="content" class="table-content">

    @php $this->layout->loadView('layout/alerts'); @endphp

    <div id="filter_results">
        @php $this->layout->loadView('payments/partial_payments_table'); @endphp
    </div>

</div>
