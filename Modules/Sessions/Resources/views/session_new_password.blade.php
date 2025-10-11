
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
    <title>{{ get_setting('custom_title', 'InvoicePlane', true) }} - @lang('set_new_password')</title>

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

    <div id="password_reset" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 col-sm-8 col-sm-offset-2 w-full md:w-1/2 px-4 col-md-offset-3">

        <h3>@lang('set_new_password')</h3>

        <br/>

        <div class="flex flex-wrap -mx-4">@include('layout.alerts')</div>

        <form method="post" action="{{ url('sessions/passwordreset') " }}>

            @csrf

            <input name="token" value="{{ $token }}" class="hidden">
            <input name="user_id" value="{{ $user_id }}" class="hidden">

            <div class="mb-4">
                <label for="new_password" class="control-label">@lang('new_password')</label>
                <input type="password" name="new_password" id="new_password" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                       placeholder="@lang('new_password')" required autofocus>
            </div>

            <input type="hidden" name="btn_new_password" value="true">

            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                <i class="fa fa-key fa-margin"></i> @lang('set_new_password')
            </button>

        </form>

    </div>

</div>

</body>
</html>
