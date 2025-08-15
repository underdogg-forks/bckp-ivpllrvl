@php namespace Modules\Units\Views; @endphp
<form method="post">

    @csrf

    <div id="headerbar">
        <h1 class="headerbar-title">@lang('add_unit')</h1>
        @include('layout.header_buttons')
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">

                @include('layout.alerts')

                <input class="hidden" name="is_update" type="hidden"
                    @if($this->mdl_units->form_value('is_update')) {
    echo 'value="1"';
} else {
    echo 'value="0"';
} @endphp
                >

                <div class="form-group">
                    <label for="unit_name">
                        @lang('unit_name')
                    </label>
                    <input type="text" name="unit_name" id="unit_name" class="form-control"
                           value="{{ $this->mdl_units->form_value('unit_name', true) }}" required>
                </div>

                <div class="form-group">
                    <label for="unit_name_plrl">
                        @lang('unit_name_plrl')
                    </label>
                    <input type="text" name="unit_name_plrl" id="unit_name_plrl" class="form-control"
                           value="{{ $this->mdl_units->form_value('unit_name_plrl', true) }}" required>
                </div>

            </div>
        </div>

    </div>

</form>
