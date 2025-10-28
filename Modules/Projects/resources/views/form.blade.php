
<script>
    $(function () {
        @php
            $this->layout->loadView('clients/script_select2_client_id.js');
            ?>
                });
            </script>

            <form method="post">

                @csrf

        <div id="headerbar">
        <h1 class="headerbar-title">@lang('projects_form')</h1>
        @include('layout.header_buttons')
        </div>

        <div id="content">

            @include('layout.alerts')

            <div class="mb-4">
            <label for="project_name">@lang('project_name')</label>
            <input type="text" name="project_name" id="project_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                   value="{{ $this->mdl_projects->form_value('project_name', true) }}" required>
            </div>

            <div class="mb-4 has-feedback">
                <label for="client_id">@lang('client')</label>
                <div class="input-group">
                <span id="toggle_permissive_search_clients" class="input-group-addon"
                      title="@lang('enable_permissive_search_clients')" style="cursor:pointer;">
                    <i class="fa fa-toggle-{{ get_setting('enable_permissive_search_clients') ? 'on' : 'off' }} fa-fw"></i>
                </span>
                    <select name="client_id" id="client_id" class="client-id-select w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors" autofocus="autofocus">
                        @php $permissive = get_setting('enable_permissive_search_users');
@if(!empty($project->client_id)) {

                        <option value="{{ $project->client_id " }}>{!! format_client($project) !!}</option>
                            @endif
                    </select>
                </div>
            </div>

            <input class="hidden" id="input_permissive_search_clients"
                   value="{{ get_setting('enable_permissive_search_clients') " }}>
        </div>

    </form>
