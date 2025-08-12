@php namespace Modules\Projects\Views; @endphp
<div class="table-responsive">
    <table class="table table-hover table-striped">

        <thead>
        <tr>
            <th>@lang('project_name')</th>
            <th>@lang('client_name')</th>
            <th>@lang('options')</th>
        </tr>
        </thead>

        <tbody>
        @foreach($projects as $project)
        <tr>
            <td>{{ anchor('projects/view/' . $project->project_id, htmlsc($project->project_name)) }}</td>
            <td>{{ $project->client_id ? htmlsc(format_client($project)) : trans('none') }}</td>
            <td>
                <div class="options btn-group">
                    <a class="btn btn-default btn-sm dropdown-toggle"
                       data-toggle="dropdown" href="#">
                        <i class="fa fa-cog"></i> @lang('options')
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ url('projects/form/' . $project->project_id) }}">
                                <i class="fa fa-edit fa-margin"></i> @lang('edit')
                            </a>
                        </li>
                        <li>
                            <form action="{{ url('projects/delete/' . $project->project_id) }}"
                                  method="POST">
                                @csrf
                                <button type="submit" class="dropdown-button"
                                        onclick="return confirm('@lang('delete_record_warning')');">
                                    <i class="fa fa-trash-o fa-margin"></i> @lang('delete')
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
            <?php
        } @endphp
        </tbody>

    </table>
</div>
<?php
