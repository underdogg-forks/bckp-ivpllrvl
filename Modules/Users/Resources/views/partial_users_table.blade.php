<div class="table-responsive">
    <table class="table table-hover table-striped">

        <thead>
        <tr>
            <th>@lang('name')</th>
            <th>@lang('user_type')</th>
            <th>@lang('email_address')</th>
            <th>@lang('options')</th>
        </tr>
        </thead>

        <tbody>
        @foreach($users as $user)
        <tr>
            <td>{!! $user->user_name !!}</td>
            <td>{{ $user_types[$user->user_type] }}</td>
            <td>{{ $user->user_email }}</td>
            <td>
                <div class="options btn-group btn-group-sm">
                    @if($user->user_type == 2)
                    <a href="{{ url('user_clients/user/' . $user->user_id) }}"
                       class="btn btn-default">
                        <i class="fa fa-list fa-margin"></i> @lang('assigned_clients')
                    </a>
                    @php
                        }
                        // Endif

                    <a class="btn btn-default dropdown-toggle"
                       data-toggle="dropdown" href="#">
                        <i class="fa fa-cog"></i> @lang('options')
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ url('users/form/' . $user->user_id) }}">
                                <i class="fa fa-edit fa-margin"></i> @lang('edit')
                            </a>
                        </li>
                        @if($user->user_id !== 1)
                        <li>
                            <form action="{{ url('users/delete/' . $user->user_id) }}"
                                  method="POST">
                                @csrf
                                <button type="submit" class="dropdown-button"
                                        onclick="return confirm('@lang('delete_record_warning')');">
                                    <i class="fa fa-trash-o fa-margin"></i> @lang('delete')
                                </button>
                            </form>
                        </li>@endforeach
                    </ul>
                </div>
            </td>
        </tr>
@endforeach
</tbody>
        </table>
    </div>
