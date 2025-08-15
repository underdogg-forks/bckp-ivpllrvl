
<div class="table-responsive">
    <table class="table table-hover table-striped">

        <thead>
        <tr>
            <th>@lang('status')</th>
            <th>@lang('task_name')</th>
            <th>@lang('task_finish_date')</th>
            <th>@lang('project')</th>
            <th class="amount last">@lang('task_price')</th>
            <th>@lang('options')</th>
        </tr>
        </thead>

        <tbody>
        @foreach($tasks as $task) {
    $label_class = $task_statuses[$task->task_status]['class'] ?? '';
        @endphp
        <tr>
            <td>
                        <span class="label {{ $label_class }}">
                            {{ $task_statuses[$task->task_status]['label'] ?? '' }}
                        </span>
            </td>
            <td><a href="{{ url('tasks/form/' . $task->task_id) }}"><i class="fa fa-edit"></i> {!! $task->task_name !!}</a></td>
            <td>
                <div class="{{ $task->is_overdue ? 'text-danger' : '' }}">
                    {{ date_from_mysql($task->task_finish_date) }}
                </div>
            </td>
            <td>
                {{ empty($task->project_id) ? '' : anchor('projects/view/' . $task->project_id, htmlsc($task->project_name)) }}
            </td>
            <td class="amount last">
                {{ format_currency($task->task_price) }}
            </td>
            <td>
                <div class="options btn-group">
                    <a class="btn btn-default btn-sm dropdown-toggle"
                       data-toggle="dropdown" href="#">
                        <i class="fa fa-cog"></i> @lang('options')
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ url('tasks/form/' . $task->task_id) }}"
                               title="@lang('edit')">
                                <i class="fa fa-edit fa-margin"></i> @lang('edit')
                            </a>
                        </li>
                        @if(!($task->task_status == 4 && $this->config->item('enable_invoice_deletion') !== true))
                        <li>
                            <form action="{{ url('tasks/delete/' . $task->task_id) }}"
                                  method="POST">
                                @csrf
                                <button type="submit" class="dropdown-button"
                                        onclick="return confirm('{{ $task->task_status == 4 ? trans('alert_task_delete') : trans('delete_record_warning') }}');">
                                    <i class="fa fa-trash-o fa-margin"></i> @lang('delete')
                                </button>
                            </form>
                        </li>
                        @php
                            }
                            // end if
                        @endphp
                    </ul>
                </div>

            </td>
        </tr>
            @endif
        </tbody>

    </table>
</div>
<?php
