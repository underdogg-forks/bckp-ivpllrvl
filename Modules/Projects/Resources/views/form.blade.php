
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

            <div class="form-group">
            <label for="project_name">@lang('project_name')</label>
            <input type="text" name="project_name" id="project_name" class="form-control"
                   value="{{ $this->mdl_projects->form_value('project_name', true) }}" required>
            </div>

            <div class="form-group has-feedback">
                <label for="client_id">@lang('client')</label>
                <div class="input-group">
                <span id="toggle_permissive_search_clients" class="input-group-addon"
                      title="@lang('enable_permissive_search_clients')" style="cursor:pointer;">
                    <i class="fa fa-toggle-{{ get_setting('enable_permissive_search_clients') ? 'on' : 'off' }} fa-fw"></i>
                </span>
                    <select name="client_id" id="client_id" class="client-id-select form-control" autofocus="autofocus">
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
