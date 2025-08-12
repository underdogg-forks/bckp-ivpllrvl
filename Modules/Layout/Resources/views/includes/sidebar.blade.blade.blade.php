@php namespace Modules\Layout\Views\Includes; @endphp
<div class="sidebar hidden-xs">
    <ul>
        <li>
            <a href="{{ url('clients/index');
?>" title="@@lang('clients')"
               class="tip" data-placement="right">
                <i class="fa fa-users"></i>
            </a>
        </li>
        <li>
            <a href="{{ url('quotes/index') }}" title="@@lang('quotes')"
               class="tip" data-placement="right">
                <i class="fa fa-file"></i>
            </a>
        </li>
        <li>
            <a href="{{ url('invoices/index') }}" title="@@lang('invoices')"
               class="tip" data-placement="right">
                <i class="fa fa-file-text"></i>
            </a>
        </li>
        <li>
            <a href="{{ url('payments/index') }}" title="@@lang('payments')"
               class="tip" data-placement="right">
                <i class="fa fa-money"></i>
            </a>
        </li>
        <li>
            <a href="{{ url('products/index') }}" title="@@lang('products')"
               class="tip" data-placement="right">
                <i class="fa fa-database"></i>
            </a>
        </li>
        @php if (get_setting('projects_enabled') == 1) {
    @endphp
            <li>
                <a href="{{ site_url('tasks/index') }}" title="@php
    @@lang('tasks') }}"
                   class="tip" data-placement="right">
                    <i class="fa fa-check-square-o"></i>
                </a>
            </li>
        <?php
} @endphp
        <li>
            <a href="{{ url('settings') }}" title="@@lang('system_settings')"
               class="tip" data-placement="right">
                <i class="fa fa-cogs"></i>
            </a>
        </li>
    </ul>
</div>
<?php 
