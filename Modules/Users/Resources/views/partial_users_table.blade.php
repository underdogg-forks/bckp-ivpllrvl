<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

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
                <div class="options inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm">
                    @if($user->user_type == 2)
                    <a href="{{ url('user_clients/user/' . $user->user_id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fa fa-list fa-margin"></i> @lang('assigned_clients')
                    </a>
                    @php
                        }
                        // Endif

                    <a class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                       data-toggle="dropdown" href="#">
                        <i class="fa fa-cog"></i> @lang('options')
                    </a>
                    <ul class="absolute z-10 mt-2 min-w-[160px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg overflow-hidden">
                        <li>
                            <a href="{{ url('users/form/' . $user->user_id) " }}>
                                <i class="fa fa-edit fa-margin"></i> @lang('edit')
                            </a>
                        </li>
                        @if($user->user_id !== 1)
                        <li>
                            <form action="{{ url('users/delete/' . $user->user_id) }}"
                                  method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
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
