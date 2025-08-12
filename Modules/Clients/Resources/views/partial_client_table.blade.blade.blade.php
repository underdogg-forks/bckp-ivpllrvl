@php namespace Modules\Clients\Views; @endphp
<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th>@@lang('active')</th>
            <th>@@lang('client_name')</th>
            <th>@@lang('email_address')</th>
@php if ($einvoicing) {
    @endphp
            <th>{{ ' e-' . trans('invoicing') . ' ' . ucfirst(trans('version')) }}</th>
            <th>{{ ' e-' . trans('invoicing') . ' ' . trans('active') }}</th>
@php
} @endphp
            <th>@@lang('phone_number')</th>
            <th class="amount last">@@lang('balance')</th>
            <th>@@lang('options')</th>
        </tr>
        </thead>
        <tbody>
@php $class_checks = ['fa fa-lg fa-check-square-o text-success', 'fa fa-lg fa-edit text-warning'];
// e-invoice
foreach ($records as $client) {
    @endphp
            <tr>
                <td>
                    {{ $client->client_active ? '<span class="label active">' . trans('yes') . '</span>' : '<span class="label inactive">' . trans('no') . '</span>' }}
                </td>
                <td>{{ anchor('clients/view/' . $client->client_id, htmlsc(format_client($client))) }}</td>
                <td>@php
    _htmlsc($client->client_email);
    @endphp</td>
@php
    if ($einvoicing) {
        @endphp
                <td>@php
        _htmlsc($client->client_einvoicing_version);
        @endphp</td>
                <td>
@php
        if ($client->client_einvoicing_active == 1) {
            @endphp
                    <i class="{{ $class_checks[0] }}"></i>
@php
        } elseif ($client->client_einvoicing_version != '') {
            @endphp
                    <i class="{{ $class_checks[1] }}"></i>
@php
        }
        @endphp
                </td>
@php
    }
    @endphp
                <td>@php
    _htmlsc($client->client_phone ? $client->client_phone : ($client->client_mobile ? $client->client_mobile : ''));
    @endphp</td>
                <td class="amount last">{{ format_currency($client->client_invoice_balance) }}</td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> @@lang('options')
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ url('clients/view/' . $client->client_id) }}">
                                    <i class="fa fa-eye fa-margin"></i> @@lang('view')
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('clients/form/' . $client->client_id) }}">
                                    <i class="fa fa-edit fa-margin"></i> @@lang('edit')
                                </a>
                            </li>
                            <li>
                                <a href="#" class="client-create-quote"
                                   data-client-id="{{ $client->client_id }}">
                                    <i class="fa fa-file fa-margin"></i> @@lang('create_quote')
                                </a>
                            </li>
                            <li>
                                <a href="#" class="client-create-invoice"
                                   data-client-id="{{ $client->client_id }}">
                                    <i class="fa fa-file-text fa-margin"></i> @@lang('create_invoice')
                                </a>
                            </li>
                            <li>
                                <form action="{{ url('clients/delete/' . $client->client_id) }}"
                                      method="POST">
                                    @php
    _csrf_field();
    @endphp
                                    <button type="submit" class="dropdown-button"
                                            onclick="return confirm('@@lang('delete_client_warning')');">
                                        <i class="fa fa-trash-o fa-margin"></i> @@lang('delete')
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
<?php
}
// End foreach @endphp
        </tbody>
    </table>
</div>
<?php 
