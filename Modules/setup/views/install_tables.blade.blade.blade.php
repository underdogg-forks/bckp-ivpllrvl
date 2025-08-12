@php namespace Modules\Setup\Views; @endphp
<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post" class="form-horizontal" action="{{ url($this->uri->uri_string());
?>">

            @php _csrf_field(); @endphp

            <legend>@@lang('setup_install_tables')</legend>

            @php if ($errors) {
    @endphp
                <p>@php
    @@lang('setup_tables_errors') }}</p>

                @php
    foreach ($errors as $error) {
        @endphp
                    <p>
                        <span class="label label-important">
                            @@lang('failure')
                        </span>
                        {{ $error }}
                    </p>
                @php
    }
    @endphp

            @php
} else {
    @endphp
                <p>
                    <i class="fa fa-check text-success fa-margin"></i>
                    @@lang('setup_tables_success')
                </p>
            @php
} @endphp

            @php if ($errors) {
    @endphp
                <input type="submit" class="btn btn-primary" name="btn_try_again"
                       value="@@lang('try_again')">
            @php
} else {
    @endphp
                <input type="submit" class="btn btn-success" name="btn_continue"
                       value="@@lang('continue')">
            <?php
} @endphp

        </form>

    </div>
</div>
<?php 