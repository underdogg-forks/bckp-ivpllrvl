@php namespace Modules\Payments\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@@lang('payment_logs')</h1>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('payments/online_logs'), 'mdl_payment_logs') }}
    </div>

</div>

<div id="content" class="table-content">

    @php $this->layout->loadView('layout/alerts'); @endphp

    <div id="filter_results">
        @php $this->layout->loadView('payments/partial_online_logs_table'); @endphp
    </div>

</div>
<?php 