
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
?> - @lang('login')</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width">
    <meta name="robots" content="NOINDEX,NOFOLLOW">

    <link rel="icon" href="@php _core_asset('img/favicon.png')" type="image/png">

    <link rel="stylesheet" href="{{ _theme_asset('css/style.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ _core_asset('css/custom.css') }}" type="text/css">
</head>

<body>

<noscript>
    <div class="p-4 mb-4 text-red-700 dark:text-red-200 bg-red-100 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg no-margin">@lang('please_enable_js')</div>
</noscript>

<br>

<div class="container">

    <div id="login" class="col-sm-8 col-sm-offset-2 w-full md:w-1/2 px-4 col-md-offset-3">

@if($login_logo)
            <img src="{{ url() }}uploads/{{ $login_logo }}" class="login-logo img-responsive">
        @else
        <h1>@php
            @lang('login')</h1>
        @endif

        <div class="flex flex-wrap -mx-4">@include('layout.alerts')</div>

        <form method="post" action="{{ url($this->uri->uri_string()) " }}>

        @csrf

        <div class="mb-4">
        <label for="email" class="control-label">@lang('email')</label>
        <input type="email" name="email" id="email" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
        placeholder="@lang('email')" required autofocus
        >
        </div>

        <div class="mb-4">
        <label for="password" class="control-label">@lang('password')</label>
        <input type="password" name="password" id="password" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
        placeholder="@lang('password')" required
        >
        </div>

        <input type="hidden" name="btn_login" value="true">

        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
        <i class="fa fa-unlock fa-margin"></i> @lang('login')
        </button>
        <a href="{{ url('sessions/passwordreset') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
        @lang('forgot_your_password')
        </a>

        </form>

        </div>
        </div>

        </body>
        </html>
