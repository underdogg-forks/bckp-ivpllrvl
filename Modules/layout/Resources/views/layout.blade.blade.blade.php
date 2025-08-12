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
    @php // GetController the page head content
$this->layout->loadView('layout/includes/head'); @endphp
</head>
<body class="{{ get_setting('disable_sidebar') ? 'hidden-sidebar' : '' }}">

<noscript>
    <div class="alert alert-danger no-margin">@@lang('please_enable_js')</div>
</noscript>

@php // GetController the navigation bar
$this->layout->loadView('layout/includes/navbar'); @endphp

<div id="main-area">
    @php // Display the sidebar if enabled
if (get_setting('disable_sidebar') != 1) {
    $this->layout->loadView('layout/includes/sidebar');
} @endphp
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
