
<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <h2>@lang('setup_complete')</h2>

        <p>
            @lang('setup_complete_message')
        </p>

        <p class="alert alert-info">
            @lang('setup_complete_support_note')
        </p>

        <p class="alert alert-warning">
            @lang('setup_complete_secure_setup')
        </p>

        @if($this->session->userdata('setup_notice')) {
    $setup_notice = $this->session->userdata('setup_notice');
        @endphp
        <div class="alert {{ $setup_notice['type'] }}">
            {{ $setup_notice['content'] }}
        </div>
            @endif

        <a href="{{ url('sessions/login') }}" class="btn btn-success">
            <i class="fa fa-check fa-margin"></i> @lang('login')
        </a>

    </div>
</div>
<?php
