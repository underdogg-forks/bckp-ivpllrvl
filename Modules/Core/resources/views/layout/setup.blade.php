
    <!DOCTYPE html>

<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="@lang('cldr')"> <![endif]-->
<!--[if IE 7]>
<html class="no-js ie7 oldie" lang="@lang('cldr')"> <![endif]-->
<!--[if IE 8]>
<html class="no-js ie8 oldie" lang="@lang('cldr')"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="@lang('cldr')"> <!--<![endif]-->

<head>
    <title>InvoicePlane Setup</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width">
    <meta name="robots" content="NOINDEX,NOFOLLOW">

    <link rel="icon" href="@php _core_asset('img/favicon.png')" type="image/png">

    <link rel="stylesheet" href="{{ _theme_asset('css/welcome.css') }}" type="text/css">
    <!--[if lt IE 9]>
    <script src="{{ _core_asset('js/legacy.min.js') " }}></script>
    <![endif]-->

    <script src="{{ _core_asset('js/dependencies.min.js') " }}></script>

</head>
<body>

<noscript>
    <div class="p-4 mb-4 text-red-700 dark:text-red-200 bg-red-100 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg no-margin">@lang('please_enable_js')</div>
</noscript>

{{ $content }}

<script>$('.simple-select').select2();</script>

</body>
</html>
