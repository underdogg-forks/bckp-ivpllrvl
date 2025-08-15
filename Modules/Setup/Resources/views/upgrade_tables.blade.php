
<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post" class="form-horizontal" action="{{ url($this->uri->uri_string());
?>">

            @csrf

            <legend>@lang('setup_upgrade_tables')</legend>

            @if($errors)
                <p>
                    @php
    @lang('setup_upgrade_message') }}
                </p>

                @foreach($errors as $error)
                    <p>
                        <i class=" fa fa-close text-danger fa-margin
        "></i>
        {{ $error }}
        </p>
        @endif

        @else
        <p>
            <i class="fa fa-check text-success fa-margin"></i>
            @lang('setup_upgrade_success')
        </p>
        @endif

        @if($errors)
        <input type="submit" class="btn btn-danger" name="btn_try_again"
               value="@lang('try_again')">
        @else
        <input type="submit" class="btn btn-success" name="btn_continue"
               value="@lang('continue')">
            @endif

        </form>

    </div>
</div>
<script>window.scrollTo(0, document.body.scrollHeight);</script>
<?php
