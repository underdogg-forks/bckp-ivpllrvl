@php namespace Modules\Tasks\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@@lang('tasks')</h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="{{ url('tasks/form') }}">
            <i class="fa fa-plus"></i> @@lang('new')
        </a>
    </div>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('tasks/index'), 'mdl_tasks') }}
    </div>

</div>

<div id="content" class="table-content">

    @php $this->layout->loadView('layout/alerts'); @endphp

    <div id="filter_results">
        @php $this->layout->loadView('tasks/partial_tasks_table'); @endphp
    </div>

</div>
