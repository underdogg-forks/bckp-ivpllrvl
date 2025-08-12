@php namespace Modules\Projects\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@php
_htmlsc($project->project_name);
?></h1>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
            <a href="{{ url('tasks/form/') }}" class="btn btn-default">
                <i class="fa fa-check-square-o fa-margin"></i>@@lang('new_task')
            </a>
            <a href="{{ url('projects/form/' . $project->project_id) }}" class="btn btn-default">
                <i class="fa fa-edit"></i> @@lang('edit')
            </a>
            <a class="btn btn-danger"
               href="{{ url('projects/delete/' . $project->project_id) }}"
               onclick="return confirm('@@lang('delete_record_warning')');">
                <i class="fa fa-trash-o"></i> @@lang('delete')
            </a>
        </div>
    </div>
</div>

<div id="content">

    <div class="row">
        <div class="col-xs-12 col-md-4">
@php if (!empty($project->client_name)) {
    @endphp
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>@php
    _htmlsc(format_client($project));
    @endphp</strong>
                </div>
                <div class="panel-body">
                    <div class="client-address">
                        @php
    $this->layout->loadView('clients/partial_client_address', ['client' => $project]);
    @endphp
                    </div>
                </div>
            </div>
@php
} else {
    @endphp
            <div class="alert alert-info">@@lang('alert_no_client_assigned')</div>
@php
} @endphp
        </div>
        <div class="col-xs-12 col-md-8">

            <div class="panel panel-default">
                <div class="panel-heading">
                    @@lang('tasks')
                </div>
                <div class="panel-body no-padding">

                    <div class="table-responsive">
                        <table class="table table-hover table-striped no-margin">

                            <thead>
                            <tr>
                                <th>@@lang('task_name')</th>
                                <th>@@lang('status')</th>
                                <th>@@lang('task_finish_date')</th>
                                <th>@@lang('project')</th>
                            </tr>
                            </thead>

                            <tbody>
@php foreach ($tasks as $task) {
    @endphp
                                <tr>
                                    <td>{{ anchor('tasks/form/' . $task->task_id, htmlsc($task->task_name)) }}</td>
                                    <td>
                                        <span class="label {{ $task_statuses[$task->task_status]['class'] }}">
                                            {{ $task_statuses[$task->task_status]['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="{{ $task->is_overdue ? 'text-danger' : '' }}">
                                            {{ date_from_mysql($task->task_finish_date) }}
                                        </span>
                                    </td>
                                    <td>{{ anchor('projects/form/' . $project->project_id, htmlsc($project->project_name)) }}</td>
                                </tr>
@php
} @endphp
                            </tbody>
                        </table>
                    </div>
                </div>
@php if (empty($tasks)) {
    @endphp
                <div class="panel-body">
                    <div class="alert alert-info no-margin">{{ trans('alert_no_tasks_found') }}</div>
                </div>
<?php
} @endphp
            </div>
        </div>
    </div>
</div>
<?php 