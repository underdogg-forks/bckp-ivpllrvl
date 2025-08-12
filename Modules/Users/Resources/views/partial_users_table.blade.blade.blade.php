@php namespace Modules\Users\Views; @endphp
    <div class="table-responsive">
        <table class="table table-hover table-striped">

            <thead>
            <tr>
                <th>@@lang('name')</th>
                <th>@@lang('user_type')</th>
                <th>@@lang('email_address')</th>
                <th>@@lang('options')</th>
            </tr>
            </thead>

            <tbody>
@php foreach ($users as $user) {
    @endphp
                <tr>
                    <td>@php
    _htmlsc($user->user_name);
    @endphp</td>
                    <td>{{ $user_types[$user->user_type] }}</td>
                    <td>{{ $user->user_email }}</td>
                    <td>
                        <div class="options btn-group btn-group-sm">
@php
    if ($user->user_type == 2) {
        @endphp
                        <a href="{{ url('user_clients/user/' . $user->user_id) }}"
                           class="btn btn-default">
                            <i class="fa fa-list fa-margin"></i> @@lang('assigned_clients')
                        </a>
@php
    }
    // Endif
    @endphp
                            <a class="btn btn-default dropdown-toggle"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> @@lang('options')
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ url('users/form/' . $user->user_id) }}">
                                        <i class="fa fa-edit fa-margin"></i> @@lang('edit')
                                    </a>
                                </li>
@php
    if ($user->user_id !== 1) {
        @endphp
                                    <li>
                                        <form action="{{ url('users/delete/' . $user->user_id) }}"
                                              method="POST">
                                            @php
        _csrf_field();
        @endphp
                                            <button type="submit" class="dropdown-button"
                                                    onclick="return confirm('@@lang('delete_record_warning')');">
                                                <i class="fa fa-trash-o fa-margin"></i> @@lang('delete')
                                            </button>
                                        </form>
                                    </li>
@php
    }
    @endphp
                            </ul>
                        </div>
                    </td>
                </tr>
<?php
}
// End foreach @endphp
            </tbody>
        </table>
    </div>
<?php 
