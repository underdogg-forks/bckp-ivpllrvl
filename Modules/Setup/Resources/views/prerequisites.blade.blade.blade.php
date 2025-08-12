@php namespace Modules\Setup\Views; @endphp
<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>
        <form method="post" class="form-horizontal" action="{{ url($this->uri->uri_string());
?>">

            @php _csrf_field(); @endphp

            <legend>@@lang('setup_prerequisites')</legend>

            <p>@@lang('setup_prerequisites_message')</p>

@php foreach ($basics as $basic) {
    if (isset($basic['warning'])) {
        @endphp
            <p><i class="fa fa-exclamation text-warning fa-margin"></i> {{ $basic['message'] }}</p>
@php
    } elseif ($basic['success'] == 1) {
        @endphp
            <p><i class="fa fa-check text-success fa-margin"></i> {{ $basic['message'] }}</p>
@php
    } else {
        $errors = true;
        @endphp
            <p><i class="fa fa-close text-danger fa-margin"></i> {{ $basic['message'] }}</p>
@php
    }
} @endphp

            <br>

@php foreach ($writables as $writable) {
    if ($writable['success'] === 1) {
        @endphp
            <p><i class="fa fa-check text-success fa-margin"></i> {{ $writable['message'] }}</p>
@php
    } else {
        @endphp
            <p><i class="fa fa-close text-danger fa-margin"></i> {{ $writable['message'] }}</p>
@php
    }
} @endphp

@php if ($errors) {
    @endphp
            <a href="javascript:history.go(0)" class="btn btn-danger">
                @php
    @@lang('try_again') }}
            </a>
@php
} else {
    @endphp
            <input class="btn btn-success" type="submit" name="btn_continue"
                   value="@@lang('continue')">
<?php
} @endphp

        </form>

    </div>
</div>
<?php 
