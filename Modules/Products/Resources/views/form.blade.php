
<form method="post">

    @php
        _csrf_field();
        ?>

            <div id="headerbar">
                <h1 class="headerbar-title">@lang('products_form')</h1>
                @include('layout.header_buttons')
    </div>

    <div id="content">

        @include('layout.alerts')

        <div class="row">
            <div class="col-xs-12 col-md-6">

                <div class="panel panel-default">
                    <div class="panel-heading">

                        @if($this->mdl_products->form_value('product_id'))
                        #{{ $this->mdl_products->form_value('product_id') }}&nbsp;
                        {{ $this->mdl_products->form_value('product_name', true);
} else {

                        @php
    @lang('new_product');
}

                    </div>
                    <div class="panel-body">

                        <div class="form-group">
                            <label for="family_id">
                                @lang('family')
                            </label>

                            <select name="family_id" id="family_id" class="form-control simple-select">
                                <option value="0">@lang('select_family')</option>
@foreach($families as $family)
                                <option value="{{ $family->family_id }}"
                        @php
                            check_select($this->mdl_products->form_value('family_id'), $family->family_id) }}
                                                        >{{ $family->family_name }}</option>@endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="product_sku">
                            @lang('product_sku')
                        </label>

                        <input type="text" name="product_sku" id="product_sku" class="form-control"
                               value="{{ $this->mdl_products->form_value('product_sku', true) }}">
                    </div>

                    <div class="form-group">
                        <label for="product_name">
                            @lang('product_name')
                        </label>

                        <input type="text" name="product_name" id="product_name" class="form-control" required
                               value="{{ $this->mdl_products->form_value('product_name', true) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="product_description">
                            @lang('product_description')
                        </label>

                        <textarea name="product_description" id="product_description" class="form-control"
                                  rows="3">{{ $this->mdl_products->form_value('product_description', true) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="product_price">
                            @lang('product_price')
                        </label>

                        <div class="input-group has-feedback">
                            <input type="text" name="product_price" id="product_price" class="form-control"
                                   value="{{ format_amount($this->mdl_products->form_value('product_price')) }}"
                                   required>
                            <span class="input-group-addon">{{ get_setting('currency_symbol') }}</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="unit_id">
                            @lang('product_unit')
                        </label>

                        <select name="unit_id" id="unit_id" class="form-control simple-select">
                            <option value="0">@lang('select_unit')</option>
                            @foreach($units as $unit)
                            <option value="{{ $unit->unit_id }}"
                                @php
                                    check_select($this->mdl_products->form_value('unit_id'), $unit->unit_id)
                            >{{ $unit->unit_name . '/' . $unit->unit_name_plrl }}</option>@endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tax_rate_id">
                            @lang('tax_rate')
                        </label>

                        <select name="tax_rate_id" id="tax_rate_id" class="form-control simple-select">
                            <option value="0">@lang('none')</option>
                            @foreach($tax_rates as $tax_rate)
                            <option value="{{ $tax_rate->tax_rate_id }}"
                                @php
                                    check_select($this->mdl_products->form_value('tax_rate_id'), $tax_rate->tax_rate_id)
                            >{{ $tax_rate->tax_rate_name . ' (' . format_amount($tax_rate->tax_rate_percent) . '%)' }}</option>@endforeach
                        </select>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-xs-12 col-md-6">

            <div class="panel panel-default">
                <div class="panel-heading">
                    @lang('extra_information')
                </div>
                <div class="panel-body">

                    <div class="form-group">
                        <label for="provider_name">
                            @lang('provider_name')
                        </label>

                        <input type="text" name="provider_name" id="provider_name" class="form-control"
                               value="{{ $this->mdl_products->form_value('provider_name', true) }}">
                    </div>

                    <div class="form-group">
                        <label for="purchase_price">
                            @lang('purchase_price')
                        </label>

                        <div class="input-group has-feedback">
                            <input type="text" name="purchase_price" id="purchase_price" class="form-control"
                                   value="{{ format_amount($this->mdl_products->form_value('purchase_price')) }}">
                            <span class="input-group-addon">{{ get_setting('currency_symbol') }}</span>
                        </div>
                    </div>

                </div>
            </div>
            @if(get_setting('sumex') == '1')

            <div class="panel panel-default">
                <div class="panel-heading">
                    @lang('invoice_sumex')
                </div>
                <div class="panel-body">

                    <div class="form-group">
                        <label for="product_tariff">
                            @lang('product_tariff')
                        </label>

                        <input type="text" name="product_tariff" id="product_tariff" class="form-control"
                               value="{{ $this->mdl_products->form_value('product_tariff', true) }}">
                    </div>

                </div>
            </div>
            @endif
        </div>
    </div>

    </div>

</form>
<?php
