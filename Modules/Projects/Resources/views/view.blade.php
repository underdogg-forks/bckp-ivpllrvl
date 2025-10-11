
<div id="headerbar">
    <h1 class="headerbar-title">{!! $project->project_name);
            ?></h1>

                <div class="headerbar-item float-right">
                    <div class="inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm">
                        <a href="{{ url('tasks/form/') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fa fa-check-square-o fa-margin"></i>@lang('new_task')
                        </a>
                        <a href="{{ url('projects/form/' . $project->project_id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fa fa-edit"></i> @lang('edit')
                        </a>
                        <a class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                           href="{{ url('projects/delete/' . $project->project_id) }}"
                           onclick="return confirm('@lang('delete_record_warning')');">
                            <i class="fa fa-trash-o"></i> @lang('delete')
                        </a>
                    </div>
                </div>
            </div>

            <div id="content">

                <div class="flex flex-wrap -mx-4">
                    <div class="w-full px-4 md:w-1/3">
            @if(!empty($project->client_name))
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <strong>@php
                        htmlspecialchars(format_client($project) !!}</strong>
            </div>
            <div class="p-6">
                <div class="client-address">
                    @include('clients/partial_client_address', ['client' => $project])
                </div>
            </div>
        </div>
        @else
        <div class="p-4 mb-4 text-cyan-700 dark:text-cyan-200 bg-cyan-100 dark:bg-cyan-900/50 border border-cyan-200 dark:border-cyan-800 rounded-lg">@lang('alert_no_client_assigned')</div>
    @endif
</div>
<div class="w-full px-4 col-md-8">

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
            @lang('tasks')
        </div>
        <div class="p-6 no-padding">

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 no-margin">

                    <thead>
                    <tr>
                        <th>@lang('task_name')</th>
                        <th>@lang('status')</th>
                        <th>@lang('task_finish_date')</th>
                        <th>@lang('project')</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($tasks as $task)
                    <tr>
                        <td>{{ anchor('tasks/form/' . $task->task_id, htmlsc($task->task_name)) }}</td>
                        <td>
                                        <span class="label {{ $task_statuses[$task->task_status]['class']" }}>
                                            {{ $task_statuses[$task->task_status]['label'] }}
                                        </span>
                        </td>
                        <td>
                                        <span class="{{ $task->is_overdue ? 'text-danger' : ''" }}>
                                            {{ date_from_mysql($task->task_finish_date) }}
                                        </span>
                        </td>
                        <td>{{ anchor('projects/form/' . $project->project_id, htmlsc($project->project_name)) }}</td>
                    </tr>@endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if(empty($tasks))
        <div class="p-6">
            <div class="p-4 mb-4 text-cyan-700 dark:text-cyan-200 bg-cyan-100 dark:bg-cyan-900/50 border border-cyan-200 dark:border-cyan-800 rounded-lg no-margin">{{ __('alert_no_tasks_found') }}</div>
        </div>@endforeach
    </div>
</div>
</div>
</div>
