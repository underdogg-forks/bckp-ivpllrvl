
<div class="table-responsive">
    <table class="table table-hover table-striped">

        <thead>
        <tr>
            <th>@lang('family_name')</th>
            <th>@lang('options')</th>
        </tr>
        </thead>

        <tbody>
        @foreach($families as $family)
        <tr>
            <td><a href="{{ url('families/form/' . $family->family_id) }}"><i class="fa fa-edit"></i> {!! $family->family_name !!}</a></td>
            <td>
                <div class="options btn-group">
                    <a class="btn btn-default btn-sm dropdown-toggle"
                       data-toggle="dropdown" href="#">
                        <i class="fa fa-cog"></i> @lang('options')
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ url('families/form/' . $family->family_id) }}">
                                <i class="fa fa-edit fa-margin"></i> @lang('edit')
                            </a>
                        </li>
                        <li>
                            <form action="{{ url('families/delete/' . $family->family_id) }}"
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
<?php
