@php namespace Modules\Layout\Views; @endphp
<!DOCTYPE html>

<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="@@lang('cldr')"> <![endif]-->
<!--[if IE 7]>
<html class="no-js ie7 oldie" lang="@@lang('cldr')"> <![endif]-->
<!--[if IE 8]>
<html class="no-js ie8 oldie" lang="@@lang('cldr')"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="@@lang('cldr')"> <!--<![endif]-->

<head>
    <title>{{ get_setting('custom_title', 'InvoicePlane', true) }}</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="NOINDEX,NOFOLLOW">
    <meta name="_csrf" content="{{ $this->security->get_csrf_hash() }}">
    <meta name="csrf_token_name" content="{{ config_item('csrf_token_name') }}">
    <meta name="csrf_cookie_name" content="{{ config_item('csrf_cookie_name') }}">
    <meta name="legacy_calculation" content="{{ (int) config_item('legacy_calculation') }}">

    <link rel="icon" href="@php _core_asset('img/favicon.png'); @endphp" type="image/png">

    <link rel="stylesheet" href="@php _theme_asset('css/style.css'); @endphp" type="text/css">
    <link rel="stylesheet" href="@php _core_asset('css/custom.css'); @endphp" type="text/css">

@php if (get_setting('monospace_amounts') == 1) {
    @endphp
    <link rel="stylesheet" href="@php
    _theme_asset('css/monospace.css');
    @endphp" type="text/css">
@php
} @endphp

    <!--[if lt IE 9]>
    <script src="@php _core_asset('js/legacy.min.js'); @endphp"></script>
    <![endif]-->

    <script src="@php _core_asset('js/dependencies.min.js'); @endphp"></script>

</head>
<body class="{{ get_setting('disable_sidebar') ? 'hidden-sidebar' : '' }}">

<nav class="navbar navbar-inverse" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle"
                    data-toggle="collapse" data-target="#ip-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                {{ trans('menu') }} &nbsp; <i class="fa fa-bars"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="ip-navbar-collapse">
            <ul class="nav navbar-nav">
                <li>{{ anchor('guest', trans('dashboard')) }}</li>
                <li>{{ anchor('guest/quotes/index', trans('quotes')) }}</li>
                <li>{{ anchor('guest/invoices/index', trans('invoices')) }}</li>
                <li>{{ anchor('guest/payments/index', trans('payments')) }}</li>
            </ul>

            <ul class="nav navbar-nav navbar-right settings">
                <li>
                    <a href="{{ url('sessions/logout') }}"
                       class="tip icon logout" data-placement="bottom"
                       title="@@lang('logout')">
                        <span class="visible-xs">&nbsp;@@lang('logout')</span>
                        <i class="fa fa-power-off"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div id="main-area">

    <div class="sidebar hidden-xs{{ get_setting('disable_sidebar') == 1 ? ' hidden' : '' }}">
        <ul>
            <li>
                <a href="{{ url('guest') }}" title="@@lang('dashboard')" class="tip"
                   data-placement="right">
                    <i class="fa fa-dashboard"></i>
                </a>
            </li>
            <li>
                <a href="{{ url('guest/quotes/index') }}" title="@@lang('quotes')"
                   class="tip"
                   data-placement="right">
                    <i class="fa fa-file"></i>
                </a>
            </li>
            <li>
                <a href="{{ url('guest/invoices/index') }}" title="@@lang('invoices')"
                   class="tip" data-placement="right">
                    <i class="fa fa-file-text"></i>
                </a>
            </li>
            <li>
                <a href="{{ url('guest/payments/index') }}" title="@@lang('payments')"
                   class="tip" data-placement="right">
                    <i class="fa fa-money"></i>
                </a>
            </li>
        </ul>
    </div>

    <div id="main-content">
        {{ $content }}
    </div>

</div>

<div id="modal-placeholder"></div>

{{ $this->layout->loadView('layout/includes/fullpage-loader') }}

<script defer src="@php _core_asset('js/scripts.min.js'); @endphp"></script>
@php if (trans('cldr') != 'en') {
    @endphp
    <script src="@php
    _core_asset('js/locales/bootstrap-datepicker.' . trans('cldr') . '.js');
    @endphp"></script>
<?php
} @endphp

</body>
</html>
<?php 
