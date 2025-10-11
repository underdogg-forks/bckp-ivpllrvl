
<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post" class="form-horizontal" action="{{ url($this->uri->uri_string());
?>">

            @csrf

            <legend>@lang('setup_install_tables')</legend>

            @if($errors)
                <p>@php
    @lang('setup_tables_errors')</p>

                @foreach($errors as $error)
                    <p>
                        <span class=" label label-important
        ">
        @lang('failure')
        </span>
        {{ $error }}
        </p>@endforeach

        @else
        <p>
            <i class="fa fa-check text-success fa-margin"></i>
            @lang('setup_tables_success')
        </p>@endforeach

        @if($errors)
        <input type="submit" class="btn btn-primary" name="btn_try_again"
               value="@lang('try_again')">
        @else
        <input type="submit" class="btn btn-success" name="btn_continue"
               value="@lang('continue')">@endforeach

        </form>

    </div>
</div>
