@php namespace Modules\Setup\Views; @endphp
<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post" action="{{ url($this->uri->uri_string());
?>">

            @csrf

            <legend>@lang('setup_choose_language')</legend>

            <p>@lang('setup_choose_language_message')</p>

            <select name="ip_lang" class="form-control simple-select">
@foreach($languages as $language)
                <option value="{{ $language }}"{{ $language == 'en' ? ' selected="selected"' : '' }}>{{ ucfirst(str_replace('/', '', $language)) }}</option>
<?php
} @endphp
            </select>

            <br/>

            <input class="btn btn-success" type="submit" name="btn_continue" value="@lang('continue') }}">

        </form>

    </div>
</div>
<?php
