@php namespace Modules\Welcome\Views;

$completed = env_bool('SETUP_COMPLETED') ? '' : ' hidden';
$disabled = env_bool('DISABLE_SETUP') ? ' hidden' : ''; @endphp<!doctype html>

<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>
<html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>
<html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->

<head>
    <meta charset="utf-8">

    <!-- Use the .htaccess and remove these lines to avoid edge case issues -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>{{ get_setting('custom_title', 'InvoicePlane', true) }}</title>

    <!-- Mobile viewport optimized: j.mp/bplateviewport -->
    <meta name="viewport" content="width=device-width">

    <link rel="icon" href="@php _core_asset('img/favicon.png'); @endphp" type="image/png">

    <!-- CSS: implied media=all -->
    <link rel="stylesheet" href="@php _theme_asset('css/welcome.css'); @endphp" type="text/css">
    <!-- end CSS-->
</head>
<body>

<div class="container">

    <div id="content">
        <div id="logo"><span>InvoicePlane</span></div>
        <p class="alert alert-info text-center{{ $completed ? '' : ' hidden' }}">
            Please install InvoicePlane.<br/>
            <span class="text-muted">Bitte installiere InvoicePlane.</span><br/>
            <span class="text-muted">S'il vous plaît installer InvoicePlane</span><br/>
            <span class="text-muted">Por favor, instale InvoicePlane</span><br/>
        </p>

        <div class="btn-group btn-group-justified">
            <a href="{{ url() }}" class="btn btn-default{{ $completed }}">
                <i class="fa fa-user"></i> Enter
            </a>
            <a href="{{ url('setup') }}" class="btn btn-success{{ $disabled }}">
                <i class="fa fa-cogs"></i> Setup
            </a>
            <a href="https://wiki.invoiceplane.com/" class="btn btn-info">
                <i class="fa fa-info-circle"></i> Get Help
            </a>
        </div>
    </div>

</div>

</body>
</html>
