@php namespace Modules\UserClients\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@lang('assigned_clients')</h1>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
            <a class="btn btn-default" href="{{ url('users') }}">
                <i class="fa fa-arrow-left"></i> @lang('back')
            </a>
            <a class="btn btn-primary" href="{{ url('user_clients/create/' . $id) }}">
                <i class="fa fa-plus"></i> @lang('new')
            </a>
        </div>
    </div>
</div>

<div id="content">

    @include('layout.alerts')

    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">

            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('user') . ': ' . htmlsc($user->user_name) }}
                </div>

                <div class="panel-body table-content">
                    <div class="table-responsive no-margin">
                        <table class="table table-hover table-striped no-margin">

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
                                    <a href="{{ url('clients/view/' . $user_client->client_id) }}">
                                        {!! format_client($user_client) !!}
                                    </a>
                                </td>
                                <td>
                                    <form
                                        action="{{ url('user_clients/delete/' . $user_client->user_client_id) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-default btn-sm"
                                                onclick="return confirm('@lang('delete_user_client_warning')');">
                                            <i class="fa fa-trash-o fa-margin"></i> @lang('remove')
                                        </button>
                                    </form>
                                </td>
                            </tr>
                                @endif
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
<?php
