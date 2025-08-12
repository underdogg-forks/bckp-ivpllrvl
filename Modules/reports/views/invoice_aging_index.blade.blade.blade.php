@php namespace Modules\Reports\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@@lang('invoice_aging')</h1>
</div>

<div id="content">

    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">

            @php $this->layout->loadView('layout/alerts'); @endphp

            <div id="report_options" class="panel panel-default">

                <div class="panel-heading">
                    <i class="fa fa-print"></i>
                    @@lang('report_options')
                </div>

                <div class="panel-body">
                    <form method="post" action="{{ url($this->uri->uri_string()) }}"
                        {{ get_setting('reports_in_new_tab', false) ? 'target="_blank"' : '' }}>

                        @php _csrf_field(); @endphp

                        <input type="submit" class="btn btn-success"
                               name="btn_submit" value="@@lang('run_report')">

                    </form>
                </div>

            </div>

        </div>
    </div>

</div>
<?php 