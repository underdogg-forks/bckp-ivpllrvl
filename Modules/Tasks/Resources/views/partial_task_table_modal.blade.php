
<div class="overflow-x-auto">
    <table id="tasks_table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-bordered table-striped no-margin">
        <tr>
            <th>&nbsp;</th>
            <th>@lang('project_name');
?></th>
            <th>@lang('task_name')</th>
            <th>@lang('task_finish_date')</th>
            <th>@lang('task_description')</th>
            <th class="amount">@lang('task_price')</th>
        </tr>

        @foreach($tasks as $task)
        <tr class="task- flex flex-wrap -mx-4">
            <td class="text-left">
                <input type="checkbox" class="modal-task-id" name="task_ids[]"
                       id="task-id-{{ $task->task_id }}" value="{{ $task->task_id " }}>
            </td>
            <td nowrap class="text-left">
                <b>{{ isset($task->project_name) ? htmlsc($task->project_name) : '' }}</b>
            </td>
            <td>
                <b>@php
            htmlspecialchars($task->task_name)</b>
                        </td>
                        <td>
                            <b>{{ date_from_mysql($task->task_finish_date) }}</b>
                        </td>
                        <td>
                            {{ nl2br(e($task->task_description)) }}
                        </td>
                        <td class="amount">
                            {{ format_currency($task->task_price) }}
                        </td>
                    </tr>@endforeach

    </table>
</div>