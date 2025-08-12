@php namespace Modules\Invoicegroups\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@@lang('invoice_groups')</h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="{{ url('invoice_groups/form') }}">
            <i class="fa fa-plus"></i> @@lang('new')
        </a>
    </div>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('invoice_groups/index'), 'mdl_invoice_groups') }}
    </div>

</div>

<div id="content" class="table-content">

    @php $this->layout->loadView('layout/alerts'); @endphp

    <div class="table-responsive">
        <table class="table table-hover table-striped">

            <thead>
            <tr>
                <th>@@lang('name')</th>
                <th>@@lang('next_id')</th>
                <th>@@lang('left_pad')</th>
                <th>@@lang('options')</th>
            </tr>
            </thead>

            <tbody>
            @php foreach ($invoice_groups as $invoice_group) {
    @endphp
                <tr>
                    <td>@php
    _htmlsc($invoice_group->invoice_group_name);
    @endphp</td>
                    <td>{{ $invoice_group->invoice_group_next_id }}</td>
                    <td>{{ $invoice_group->invoice_group_left_pad }}</td>
                    <td>
                        <div class="options btn-group">
                            <a class="btn btn-default btn-sm dropdown-toggle"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> @@lang('options')
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ url('invoice_groups/form/' . $invoice_group->invoice_group_id) }}">
                                        <i class="fa fa-edit fa-margin"></i> @@lang('edit')
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ url('invoice_groups/delete/' . $invoice_group->invoice_group_id) }}"
                                          method="POST">
                                        @php
    _csrf_field();
    @endphp
                                        <button type="submit" class="dropdown-button"
                                                onclick="return confirm('@@lang('delete_record_warning')');">
                                            <i class="fa fa-trash-o fa-margin"></i> @@lang('delete')
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php
} @endphp
            </tbody>

        </table>
    </div>

</div>
<?php 
