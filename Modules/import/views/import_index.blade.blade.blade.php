@php namespace Modules\Import\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@@lang('import_data')</h1>
</div>

<div id="content">

    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">

            @php $this->layout->loadView('layout/alerts'); @endphp

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5>@@lang('import_from_csv')</h5>
                </div>

                <div class="panel-body">
                    <form method="post" action="{{ url($this->uri->uri_string()) }}">

                        @php _csrf_field();
foreach ($files as $file) {
    @endphp
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="files[]" value="{{ $file }}">
                                {{ $file }}
                            </label>
                        </div>
<?php
} @endphp
                        <input type="submit" class="btn btn-default" name="btn_submit" value="@@lang('import')">

                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
<?php 