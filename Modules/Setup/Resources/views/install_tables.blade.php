
<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post" class="space-y-4" action="{{ url($this->uri->uri_string());
?>">

            @csrf

            <legend>@lang('setup_install_tables')</legend>

            @if($errors)
                <p>@php
    @lang('setup_tables_errors')</p>

                @foreach($errors as $error)
                    <p>
                        <span class="label label-important">
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
        <input type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" name="btn_try_again"
               value="@lang('try_again')">
        @else
        <input type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors" name="btn_continue"
               value="@lang('continue')">@endforeach

        </form>

    </div>
</div>
