@php namespace Modules\Sessions\Views; @endphp
<!doctype html>

<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>
<html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>
<html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->

<head>
    <title>{{ get_setting('custom_title', 'InvoicePlane', true);
?> - @@lang('login')</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width">
    <meta name="robots" content="NOINDEX,NOFOLLOW">

    <link rel="icon" href="@php _core_asset('img/favicon.png'); @endphp" type="image/png">

    <link rel="stylesheet" href="@php _theme_asset('css/style.css'); @endphp" type="text/css">
    <link rel="stylesheet" href="@php _core_asset('css/custom.css'); @endphp" type="text/css">
</head>

<body>

<noscript>
    <div class="alert alert-danger no-margin">@@lang('please_enable_js')</div>
</noscript>

<br>

<div class="container">

    <div id="login" class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">

@php if ($login_logo) {
    @endphp
            <img src="{{ url() }}uploads/{{ $login_logo }}" class="login-logo img-responsive">
@php
} else {
    @endphp
            <h1>@php
    @@lang('login') }}</h1>
<?php
} @endphp

        <div class="row">@php $this->layout->loadView('layout/alerts'); @endphp</div>

        <form method="post" action="{{ url($this->uri->uri_string()) }}">

            @php _csrf_field(); @endphp

            <div class="form-group">
                <label for="email" class="control-label">@@lang('email')</label>
                <input type="email" name="email" id="email" class="form-control"
                       placeholder="@@lang('email')" required autofocus
                >
            </div>

            <div class="form-group">
                <label for="password" class="control-label">@@lang('password')</label>
                <input type="password" name="password" id="password" class="form-control"
                       placeholder="@@lang('password')" required
                >
            </div>

            <input type="hidden" name="btn_login" value="true">

            <button type="submit" class="btn btn-primary">
                <i class="fa fa-unlock fa-margin"></i> @@lang('login')
            </button>
            <a href="{{ url('sessions/passwordreset') }}" class="btn btn-default">
                @@lang('forgot_your_password')
            </a>

        </form>

    </div>
</div>

</body>
</html>
<?php 
