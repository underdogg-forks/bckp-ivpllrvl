
<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <form method="post" action="{{ url($this->uri->uri_string()) " }}>

            @csrf

            <h2>@lang('setup_calculation_info')</h2>

            <p>
                @lang('setup_calculation_info_message')
            </p>

            <p class="alert alert-warning">
                @lang('setup_calculation_info_note')
            </p>

            <input type="submit" class="btn btn-success" name="btn_agree"
                   value="@lang('setup_calculation_info_btn_agree')">

            <input type="submit" class="btn btn-warning" name="btn_continue"
                   value="@lang('setup_calculation_info_btn_disagree')">

        </form>
    </div>
</div>
