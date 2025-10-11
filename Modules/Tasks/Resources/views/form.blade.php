@if($this->mdl_tasks->form_value('task_id') && $this->mdl_tasks->form_value('task_status') == 4) {
<script type="text/javascript">
    $(document).ready(function () {
        $('#task-form').find(':input').prop('disabled', 'disabled');
        $('#btn-submit').hide();
        $('#btn-cancel').prop('disabled', false);
    });
</script>
@php
    }
    ?>

    <form method="post" id="task-form">

        @csrf

<div id="headerbar">
    <h1 class="headerbar-title">@lang('tasks_form')</h1>
    @include('layout.header_buttons')
</div>

<div id="content">

    @include('layout.alerts')

    @if($this->mdl_tasks->form_value('task_id') && $this->mdl_tasks->form_value('task_status') == 4)
    <div class="p-4 mb-4 text-yellow-700 dark:text-yellow-200 bg-yellow-100 dark:bg-yellow-900/50 border border-yellow-200 dark:border-yellow-800 rounded-lg small">{{ __('info_task_readonly') }}</div>
    @endif

    <div class="flex flex-wrap -mx-4">
        <div class="w-full px-4 col-sm-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    @if($this->mdl_tasks->form_value('task_id'))
                    #{{ $this->mdl_tasks->form_value('task_id') }}&nbsp;
                    {{ $this->mdl_tasks->form_value('task_name', true);
} else {

                    @php
@lang('new_task');
}
            </div>
            <div class="p-6">

                <div class="mb-4">
                    <label for="task_name">@lang('task_name')</label>
                    <input type="text" name="task_name" id="task_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                           value="{{ $this->mdl_tasks->form_value('task_name', true) }}" required>
                </div>

                <div class="mb-4">
                    <label for="task_description">@lang('task_description')</label>
                    <textarea name="task_description" id="task_description" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors" rows="3"
                    >{{ $this->mdl_tasks->form_value('task_description', true) }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="task_price">@lang('task_price')</label>
                    <div class="input-group">
                        <input type="text" name="task_price" id="task_price" class="amount w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                               value="{{ format_amount($this->mdl_tasks->form_value('task_price')) }}" required>
                        <div class="input-group-addon">
                            {{ get_setting('currency_symbol') }}
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="tax_rate_id">@lang('tax_rate')</label>
                    <select name="tax_rate_id" id="tax_rate_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select">
                        <option value="0">@lang('none')</option>
                        @foreach($tax_rates as $tax_rate)
                        <option value="{{ $tax_rate->tax_rate_id }}"
                        @php
                            check_select($this->mdl_tasks->form_value('tax_rate_id'), $tax_rate->tax_rate_id) }}>
                                                            {{ $tax_rate->tax_rate_name . ' (' . format_amount($tax_rate->tax_rate_percent) . '%)' }}
                                                        </option>@endforeach
                    </select>
                </div>

                <div class="mb-4 has-feedback">
                    <label for="task_finish_date">@lang('task_finish_date')</label>
                    <div class="input-group">
                        <input name="task_finish_date" id="task_finish_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors datepicker"
                               value="{{ date_from_mysql($this->mdl_tasks->form_value('task_finish_date')) }}" required>
                        <div class="input-group-addon">
                            <i class="fa fa-calendar fa-fw"></i>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="task_status">@lang('status')</label>
                    <select name="task_status" id="task_status"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select" data-minimum-results-for-search="Infinity">
                        @foreach($task_statuses as $key => $status) {
    @if($this->mdl_tasks->form_value('task_status') != 4 && $key == 4) {
        continue;
    }

                        <option value="{{ $key }}" @php
                            check_select($key, $this->mdl_tasks->form_value('task_status'))>
                            {{ $status['label'] }}
                        </option>@endforeach
                    </select>
                </div>

            </div>
        </div>
    </div>

    <div class="w-full px-4 col-sm-6">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                @lang('extra_information')
            </div>
            <div class="p-6">

                <div class="mb-4">
                    <label for="project_id">@lang('project'): </label>
                    <select name="project_id" id="project_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select">
                        <option value="">@lang('select_project')</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->project_id }}"
                            @php
                                check_select($this->mdl_tasks->form_value('project_id'), $project->project_id)>
                            {{ htmlspecialchars($project->project_name, ENT_COMPAT) }}
                        </option>@endforeach
                    </select>
                </div>

            </div>
        </div>
    </div>
</div>

</div>

</form>
