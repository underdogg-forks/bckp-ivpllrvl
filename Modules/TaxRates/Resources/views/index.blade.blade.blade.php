@php namespace Modules\TaxRates\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@@lang('tax_rates')</h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="{{ url('tax_rates/form') }}">
            <i class="fa fa-plus"></i> @@lang('new')
        </a>
    </div>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('tax_rates/index'), 'mdl_tax_rates') }}
    </div>

</div>

<div id="content" class="table-content">

    {{ $this->layout->loadView('layout/alerts') }}

    <div class="table-responsive">
        <table class="table table-hover table-striped">

            <thead>
            <tr>
                <th>@@lang('tax_rate_name')</th>
                <th>@@lang('tax_rate_percent')</th>
                <th>@@lang('options')</th>
            </tr>
            </thead>

            <tbody>
            @php foreach ($tax_rates as $tax_rate) {
            @endphp
            <tr>
                <td>@php
                        _htmlsc($tax_rate->tax_rate_name);
                    @endphp</td>
                <td>{{ format_amount($tax_rate->tax_rate_percent) }}%</td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> @@lang('options')
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ url('tax_rates/form/' . $tax_rate->tax_rate_id) }}">
                                    <i class="fa fa-edit fa-margin"></i> @@lang('edit')
                                </a>
                            </li>
                            <li>
                                <form action="{{ url('tax_rates/delete/' . $tax_rate->tax_rate_id) }}"
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
