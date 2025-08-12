@php namespace Modules\Projects\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@lang('projects')</h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="{{ url('projects/form') }}">
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('projects/index'), 'mdl_projects') }}
    </div>

</div>

<div id="content" class="table-content">

    @include('layout/alerts')

    <div id="filter_results">
        @include('projects/partial_projects_table')
    </div>

</div>
