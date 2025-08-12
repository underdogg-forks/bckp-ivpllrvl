@php namespace Modules\TaxRates\Views; @endphp
<form method="post" class="form-horizontal">

    @php _csrf_field(); @endphp

    <div id="headerbar">
        <h1 class="headerbar-title">@@lang('tax_rate_form')</h1>
        @php $this->layout->loadView('layout/header_buttons'); @endphp
    </div>

    <div id="content">

        @php $this->layout->loadView('layout/alerts'); @endphp

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label class="control-label">
                    @@lang('tax_rate_name')
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="tax_rate_name" id="tax_rate_name" class="form-control"
                       value="{{ $this->mdl_tax_rates->form_value('tax_rate_name', true) }}" required>
            </div>
        </div>

        <div class="form-group has-feedback">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label class="control-label">
                    @@lang('tax_rate_percent')
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="tax_rate_percent" id="tax_rate_percent" class="form-control"
                       value="{{ format_amount($this->mdl_tax_rates->form_value('tax_rate_percent')) }}" required>
                <span class="form-control-feedback">%</span>
            </div>
        </div>

    </div>

</form>
