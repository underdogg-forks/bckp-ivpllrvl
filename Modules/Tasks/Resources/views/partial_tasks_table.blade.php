
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

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

        <tr>
            <td>
                        <span class="label {{ $label_class" }}>
                            {{ $task_statuses[$task->task_status]['label'] ?? '' }}
                        </span>
            </td>
            <td><a href="{{ url('tasks/form/' . $task->task_id) " }}><i class="fa fa-edit"></i> {!! $task->task_name !!}</a></td>
            <td>
                <div class="{{ $task->is_overdue ? 'text-danger' : ''" }}>
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
                <div class="options inline-flex rounded-md shadow-sm">
                    <a class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5"
                       data-toggle="dropdown" href="#">
                        <i class="fa fa-cog"></i> @lang('options')
                    </a>
                    <ul class="absolute z-10 mt-2 min-w-[160px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg overflow-hidden">
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
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                        onclick="return confirm('{{ $task->task_status == 4 ? trans('alert_task_delete') : trans('delete_record_warning') }}');">
                                    <i class="fa fa-trash-o fa-margin"></i> @lang('delete')
                                </button>
                            </form>
                        </li>
                        @php
                            }
                            // end if

                    </ul>
                </div>

            </td>
        </tr>@endforeach
        </tbody>

    </table>
</div>
