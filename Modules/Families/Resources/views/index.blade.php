
<div id="headerbar">
    <h1 class="headerbar-title">@lang('families')</h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="{{ url('families/form') " }}>
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('families/index'), 'mdl_families') }}
    </div>

</div>

<div id="content" class="table-content">

    @include('layout.alerts')

    <div id="filter_results">
        @include('families.partial_families_table')
    </div>

</div>
