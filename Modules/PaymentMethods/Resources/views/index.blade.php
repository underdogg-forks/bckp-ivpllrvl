
<div id="headerbar">
    <h1 class="headerbar-title">@lang('payment_methods')</h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="{{ url('payment_methods/form') }}">
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('payment_methods/index'), 'mdl_payment_methods') }}
    </div>

</div>

<div id="content" class="table-content">

    @include('layout.alerts')

    <div class="table-responsive">
        <table class="table table-hover table-striped">

            <thead>
            <tr>
                <th>@lang('payment_method')</th>
                <th>@lang('options')</th>
            </tr>
            </thead>

            <tbody>
            @foreach($payment_methods as $payment_method)
            <tr>
                <td>{!! $payment_method->payment_method_name !!}</td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle"
                           data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i>
                            @lang('options')
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ url('payment_methods/form/' . $payment_method->payment_method_id) }}">
                                    <i class="fa fa-edit fa-margin"></i>
                                    @lang('edit')
                                </a>
                            </li>
                            <li>
                                <form action="{{ url('payment_methods/delete/' . $payment_method->payment_method_id) }}"
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
            </tr>@endforeach
            </tbody>

        </table>
    </div>

</div>
<?php
