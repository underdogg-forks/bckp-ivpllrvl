@php namespace Modules\Invoices\Views; @endphp
<div class="table-responsive">
    <table class="table table-hover table-striped">

        <thead>
        <tr>
            <th>@@lang('invoice')</th>
            <th>@@lang('created')</th>
        </tr>
        </thead>

        <tbody>
        @php foreach ($invoices_archive as $invoice) {
    @endphp
            <tr>
                <td>
                    <a href="{{ url('invoices/download/' . basename($invoice)) }}"
                       title="@@lang('invoice')">
                        {{ basename($invoice) }}
                    </a>
                </td>

                <td>
                    {{ date('F d Y H:i:s.', filemtime($invoice)) }}
                </td>

            </tr>
        <?php
} @endphp
        </tbody>

    </table>
</div>
<?php 
