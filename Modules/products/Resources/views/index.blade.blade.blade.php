@php namespace Modules\Products\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@@lang('products')</h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="{{ url('products/form') }}">
            <i class="fa fa-plus"></i> @@lang('new')
        </a>
    </div>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('products/index'), 'mdl_products') }}
    </div>

</div>

<div id="content" class="table-content">

    @php $this->layout->loadView('layout/alerts'); @endphp

    <div id="filter_results">
        @php $this->layout->loadView('products/partial_products_table'); @endphp
    </div>

</div>
<?php 