
<form method="post" class="space-y-4">

    @csrf

    <div id="headerbar">
        <h1 class="headerbar-title">@lang('payment_method_form')</h1>
        @include('layout.header_buttons')
    </div>

    <div id="content">

        @include('layout.alerts')

        <input class="hidden" name="is_update" type="hidden"
            @if($this->mdl_payment_methods->form_value('is_update'))
{value="1"}
@endif else {
    echo 'value="0"';
}
        >

        <div class="mb-4">
            <div class="w-full px-4 col-sm-2 text-right text-left -xs">
                <label for="payment_method_name" class="control-label">
                    @lang('payment_method'):
                </label>
            </div>
            <div class="w-full px-4 col-sm-6">
                <input type="text" name="payment_method_name" id="payment_method_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                       value="{{ $this->mdl_payment_methods->form_value('payment_method_name', true) }}" required>
            </div>
        </div>

    </div>

</form>
