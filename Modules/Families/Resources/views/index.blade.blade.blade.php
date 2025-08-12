@php namespace Modules\Families\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@@lang('families')</h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="{{ url('families/form') }}">
            <i class="fa fa-plus"></i> @@lang('new')
        </a>
    </div>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('families/index'), 'mdl_families') }}
    </div>

</div>

<div id="content" class="table-content">

    @php $this->layout->loadView('layout/alerts'); @endphp

    <div id="filter_results">
        @php $this->layout->loadView('families/partial_families_table'); @endphp
    </div>

</div>
