
<form method="post" class="form-horizontal">

    @csrf

    <div id="headerbar">
        <h1 class="headerbar-title">@lang('payment_method_form')</h1>
        @include('layout.header_buttons')
    </div>

    <div id="content">

        @include('layout.alerts')

        <input class="hidden" name="is_update" type="hidden"
            @if($this->mdl_payment_methods->form_value('is_update')) {
    echo 'value="1"';
} else {
    echo 'value="0"';
} @endphp
        >

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_method_name" class="control-label">
                    @lang('payment_method'):
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="payment_method_name" id="payment_method_name" class="form-control"
                       value="{{ $this->mdl_payment_methods->form_value('payment_method_name', true) }}" required>
            </div>
        </div>

    </div>

</form>
