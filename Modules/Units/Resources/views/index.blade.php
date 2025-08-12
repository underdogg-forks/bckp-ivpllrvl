@php namespace Modules\Units\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@lang('units')</h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="{{ url('units/form') }}">
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('units/index'), 'mdl_units') }}
    </div>

</div>

<div id="content" class="table-content">

    @include('layout/alerts')

    <div class="table-responsive">
        <table class="table table-hover table-striped">

            <thead>
            <tr>
                <th>@lang('unit_name')</th>
                <th>@lang('unit_name_plrl')</th>
                <th>@lang('options')</th>
            </tr>
            </thead>

            <tbody>
            @foreach($units as $unit)
            <tr>
                <td>{!! $unit->unit_name !!}</td>
                <td>{!! $unit->unit_name_plrl !!}</td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle"
                           data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> @lang('options')
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ url('units/form/' . $unit->unit_id) }}">
                                    <i class="fa fa-edit fa-margin"></i> @lang('edit')
                                </a>
                            </li>
                            <li>
                                <form action="{{ url('units/delete/' . $unit->unit_id) }}"
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

</div>
<?php
