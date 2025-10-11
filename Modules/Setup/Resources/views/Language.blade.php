
<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post" action="{{ url($this->uri->uri_string());
?>">

            @csrf

            <legend>@lang('setup_choose_language')</legend>

            <p>@lang('setup_choose_language_message')</p>

            <select name="ip_lang" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select">
@foreach($languages as $language)
                <option value="{{ $language }}"{{ $language == 'en' ? ' selected="selected"' : '' }}>{{ ucfirst(str_replace('/', '', $language)) }}</option>@endforeach
            </select>

            <br/>

            <input class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors" type="submit" name="btn_continue" value="@lang('continue')">

        </form>

    </div>
</div>
