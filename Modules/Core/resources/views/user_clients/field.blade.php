
<div id="headerbar">
    <h1 class="headerbar-title">@lang('assigned_clients')</h1>

    <div class="headerbar-item float-right">
        <div class="inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm">
            <a class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" href="{{ url('users') " }}>
                <i class="fa fa-arrow-left"></i> @lang('back')
            </a>
            <a class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" href="{{ url('user_clients/create/' . $id) " }}>
                <i class="fa fa-plus"></i> @lang('new')
            </a>
        </div>
    </div>
</div>

<div id="content">

    @include('layout.alerts')

    <div class="flex flex-wrap -mx-4">
        <div class="w-full px-4 md:w-1/2 col-md-offset-3">

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    {{ trans('user') . ': ' . htmlsc($user->user_name) }}
                </div>

                <div class="p-6 table-content">
                    <div class="overflow-x-auto no-margin">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 no-margin">

                            <thead>
                            <tr>
                                <th>@lang('client')</th>
                                <th>@lang('options')</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($user_clients as $user_client)
                            <tr>
                                <td>
                                    <a href="{{ url('clients/view/' . $user_client->client_id) " }}>
                                        {!! format_client($user_client) !!}
                                    </a>
                                </td>
                                <td>
                                    <form
                                        action="{{ url('user_clients/delete/' . $user_client->user_client_id) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5"
                                                onclick="return confirm('@lang('delete_user_client_warning')');">
                                            <i class="fa fa-trash-o fa-margin"></i> @lang('remove')
                                        </button>
                                    </form>
                                </td>
                            </tr>@endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
