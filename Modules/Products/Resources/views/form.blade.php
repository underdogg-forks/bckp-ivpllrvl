<form method="post">
    @csrf
    <div id="headerbar">
        <h1 class="headerbar-title">@lang('products_form')</h1>
        @include('layout.header_buttons')
    </div>
    <div id="content">
        @include('layout.alerts')
        <div class="flex flex-wrap -mx-4">
            <div class="w-full px-4 md:w-1/2">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        @if($this->mdl_products->form_value('product_id'))
                            #{{ $this->mdl_products->form_value('product_id') }}&nbsp;
                            {{ $this->mdl_products->form_value('product_name', true) }}
                        @else
                            @lang('new_product')
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <label for="family_id">
                                @lang('family')
                            </label>
                            <select name="family_id" id="family_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select">
                                <option value="0">@lang('select_family')</option>
                                @foreach($families as $family)
                                    <option value="{{ $family->family_id }}"
                                        @php
                                            check_select($this->mdl_products->form_value('family_id'), $family->family_id) }}
                                    >{{ $family->family_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="product_sku">
                                @lang('product_sku')
                            </label>
                            <input type="text" name="product_sku" id="product_sku" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                   value="{{ $this->mdl_products->form_value('product_sku', true) " }}>
                        </div>
                        <div class="mb-4">
                            <label for="product_name">
                                @lang('product_name')
                            </label>
                            <input type="text" name="product_name" id="product_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors" required
                                   value="{{ $this->mdl_products->form_value('product_name', true) }}" required>
                        </div>
                        <div class="mb-4">
                            <label for="product_description">
                                @lang('product_description')
                            </label>
                            <textarea name="product_description" id="product_description" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                      rows="3">{{ $this->mdl_products->form_value('product_description', true) }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label for="product_price">
                                @lang('product_price')
                            </label>
                            <div class="input-group has-feedback">
                                <input type="text" name="product_price" id="product_price" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ format_amount($this->mdl_products->form_value('product_price')) }}"
                                       required>
                                <span class="input-group-addon">{{ get_setting('currency_symbol') }}</span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="unit_id">
                                @lang('product_unit')
                            </label>
                            <select name="unit_id" id="unit_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select">
                                <option value="0">@lang('select_unit')</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->unit_id }}"
                                        @php
                                            check_select($this->mdl_products->form_value('unit_id'), $unit->unit_id)
                                    >{{ $unit->unit_name . '/' . $unit->unit_name_plrl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="tax_rate_id">
                                @lang('tax_rate')
                            </label>
                            <select name="tax_rate_id" id="tax_rate_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select">
                                <option value="0">@lang('none')</option>
                                @foreach($tax_rates as $tax_rate)
                                    <option value="{{ $tax_rate->tax_rate_id }}"
                                        @php
                                            check_select($this->mdl_products->form_value('tax_rate_id'), $tax_rate->tax_rate_id)
                                    >{{ $tax_rate->tax_rate_name . ' (' . format_amount($tax_rate->tax_rate_percent) . '%)' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full px-4 md:w-1/2">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        @lang('extra_information')
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <label for="provider_name">
                                @lang('provider_name')
                            </label>
                            <input type="text" name="provider_name" id="provider_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                   value="{{ $this->mdl_products->form_value('provider_name', true) " }}>
                        </div>
                        <div class="mb-4">
                            <label for="purchase_price">
                                @lang('purchase_price')
                            </label>
                            <div class="input-group has-feedback">
                                <input type="text" name="purchase_price" id="purchase_price" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ format_amount($this->mdl_products->form_value('purchase_price')) " }}>
                                <span class="input-group-addon">{{ get_setting('currency_symbol') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @if(get_setting('sumex') == '1')
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            @lang('invoice_sumex')
                        </div>
                        <div class="p-6">
                            <div class="mb-4">
                                <label for="product_tariff">
                                    @lang('product_tariff')
                                </label>
                                <input type="text" name="product_tariff" id="product_tariff" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                                       value="{{ $this->mdl_products->form_value('product_tariff', true) " }}>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</form>
