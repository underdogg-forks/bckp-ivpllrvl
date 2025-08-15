@php namespace Modules\CustomValues\Views; @endphp
<table class="table table-bordered">

    <thead>
    <tr>
        <th>@lang('id')</th>
        <th>@lang('label')</th>
        <th>@lang('options')</th>
    </tr>
    </thead>

    <tbody>
    @foreach($elements as $element)
    <tr>
        <td>{{ $element->custom_values_id }}</td>
        <td>{!! $element->custom_values_value !!}</td>
        <td>
            <div class="options btn-group">
                <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                   href="#">
                    <i class="fa fa-cog"></i> @lang('options')
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ url('custom_values/edit/' . $element->custom_values_id) }}">
                            <i class="fa fa-edit fa-margin"></i> @lang('edit')
                        </a>
                    </li>
                    <li>
                        <form action="{{ url('custom_values/delete/' . $element->custom_values_id) }}"
                              method="POST">
                            @csrf
                            <input type="hidden" name="custom_field_id" value="{{ $id }}">
                            <button type="submit" class="dropdown-button"
                                    onclick="return confirm(`@lang('delete_record_warning')`);">
                                <i class="fa fa-trash-o fa-margin"></i> @lang('delete')
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
        @endif
    </tbody>

</table>

<?php
