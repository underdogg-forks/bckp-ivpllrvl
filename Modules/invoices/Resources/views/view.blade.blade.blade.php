@php namespace Modules\Invoices\Views;

if ($this->config->item('disable_read_only') == true) {
    $invoice->is_read_only = 0;
}
// Little helper
$its_mine = $this->session->__get('user_id') == $invoice->user_id;
$my_class = $its_mine ? 'success' : 'warning';
// visual: work with text-* alert-*
// In change user toggle & After eInvoice (name) when user required field missing
$edit_user_title = trans('edit') . ' ' . trans('user') . ' (' . trans('invoicing') . '): ' . PHP_EOL . htmlsc(format_user($invoice->user_id)); @endphp

<script>
    $(function () {
        $('.item-task-id').each(function () {
            // Disable client change if at least one item already has a task id assigned
            if ($(this).val().length > 0) {
                $('#invoice_change_client').hide();
                return false;
            }
        });

        $('.btn_add_product').click(function () {
            $('#modal-placeholder').load("{{ url('products/ajax/modal_product_lookups');
?>/" + Math.floor(Math.random() * 1000));
        });

        $('.btn_add_task').click(function () {
            $('#modal-placeholder').load("{{ url('tasks/ajax/modal_task_lookups/' . $invoice_id) }}/" + Math.floor(Math.random() * 1000));
        });

        $('.btn_add_row').click(function () {
            $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
            // Legacy:no: check items tax usage is correct (ReLoad on change)
            check_items_tax_usages();
        });

@php if (!$items) {
    @endphp
        $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
@php
} @endphp

        // Legacy:no: check items tax usage is correct (Load on change)
        $(document).on('loaded', check_items_tax_usages());

        $('#btn_create_recurring').click(function () {
            $('#modal-placeholder').load("{{ url('invoices/ajax/modal_create_recurring') }}", {
                invoice_id: {{ $invoice_id }}
            });
        });
@php if ($invoice->invoice_status_id == 1 && !$invoice->creditinvoice_parent_id) {
    @endphp

        $('#invoice_change_client').click(function () {
            $('#modal-placeholder').load("{{ site_url('invoices/ajax/modal_change_client') }}", {
                invoice_id: {{ $invoice_id }},
                client_id: "{{ $this->db->escape_str($invoice->client_id) }}",
            });
        });

        $('#invoice_change_user').click(function () {
            $('#modal-placeholder').load("{{ url('invoices/ajax/modal_change_user') }}", {
                invoice_id: {{ $invoice_id }},
                user_id: "{{ $this->db->escape_str($invoice->user_id) }}",
            });
        });
@php
}
// End if @endphp

        $('#btn_save_invoice').click(function () {
            var items = [];
            var item_order = 1;
            $('#item_table .item').each(function () {
                var row = {};
                $(this).find('input,select,textarea').each(function () {
                    if ($(this).is(':checkbox')) {
                        row[$(this).attr('name')] = $(this).is(':checked');
                    } else {
                        row[$(this).attr('name')] = $(this).val();
                    }
                });
                row['item_order'] = item_order;
                item_order++;
                items.push(row);
            });
            $.post("{{ url('invoices/ajax/save') }}", {
                    legacy_calculation: {{ (int) $legacy_calculation }},
                    invoice_id: {{ $invoice_id }},
                    invoice_number: $('#invoice_number').val(),
                    invoice_date_created: $('#invoice_date_created').val(),
                    invoice_date_due: $('#invoice_date_due').val(),
                    invoice_status_id: $('#invoice_status_id').val(),
                    invoice_password: $('#invoice_password').val(),
                    items: JSON.stringify(items),
                    invoice_discount_amount: $('#invoice_discount_amount').val(),
                    invoice_discount_percent: $('#invoice_discount_percent').val(),
                    invoice_terms: $('#invoice_terms').val(),
                    custom: $('input[name^=custom],select[name^=custom]').serializeArray(),
                    payment_method: $('#payment_method').val(),
                },
                function (data) {
                    var response = json_parse(data, {{ (int) IP_DEBUG }});
                    if (response.success === 1) {
                        window.location = "{{ url('invoices/view') }}/" + {{ $invoice_id }};
                    } else {
                        $('#fullpage-loader').hide();
                        $('.control-group').removeClass('has-error');
                        $('div.alert[class*="alert-"]').remove();
                        var resp_errors = response.validation_errors,
                            all_resp_errors = '';
                        for (var key in resp_errors) {
                            $('#' + key).parent().addClass('has-error');
                            all_resp_errors += resp_errors[key];
                        }
                        $('#invoice_form').prepend('<div class="alert alert-danger">' + all_resp_errors + '</div>');
                    }
                });
        });

        $('#btn_generate_pdf').click(function () {
            window.open('{{ url('invoices/generate_pdf/' . $invoice_id) }}', '_blank');
        });

        $('#btn_generate_xml').click(function () {
            window.open('{{ url('invoices/generate_xml/' . $invoice_id) }}', '_blank');
        });

        $(document).on('click', '.btn_delete_item', function () {
            var btn = $(this);
            var item_id = btn.data('item-id');

            // Just remove the row if no item ID is set (new row)
            if (typeof item_id === 'undefined') {
                $(this).parents('.item').remove();
                check_items_tax_usages();
            } else {
                $.post("{{ url('invoices/ajax/delete_item/' . $invoice->invoice_id) }}", {
                        'item_id': item_id,
                    },
                    function (data) {
                        var response = json_parse(data, {{ (int) IP_DEBUG }});
                        if (response.success === 1) {
                            btn.parents('.item').remove();
                        } else {
                            btn.removeClass('btn-link').addClass('btn-danger').prop('disabled', true);
                        }

                        check_items_tax_usages();
                    }
                );
            }
        });

@php if ($invoice->is_read_only != 1) {
    if (get_setting('show_responsive_itemlist') == 1) {
        @endphp
             function UpR(k) {
               var parent = k.parents('.item');
               var pos = parent.prev();
               parent.insertBefore(pos);
             }
             function DownR(k) {
               var parent = k.parents('.item');
               var pos = parent.next();
               parent.insertAfter(pos);
             }
             $(document).on('click', '.up', function () {
               UpR($(this));
             });
             $(document).on('click', '.down', function () {
               DownR($(this));
             });
@php
    } else {
        @endphp
            var fixHelper = function (e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function (index) {
                    $(this).width($originals.eq(index).width());
                });
                return $helper;
            };

            $('#item_table').sortable({
                items: 'tbody',
                helper: fixHelper,
            });
@php
    }
    @endphp

        if ($('#invoice_discount_percent').val().length > 0) {
            $('#invoice_discount_amount').prop('disabled', true);
        }

        if ($('#invoice_discount_amount').val().length > 0) {
            $('#invoice_discount_percent').prop('disabled', true);
        }

        $('#invoice_discount_amount').keyup(function () {
            if (this.value.length > 0) {
                $('#invoice_discount_percent').prop('disabled', true);
            } else {
                $('#invoice_discount_percent').prop('disabled', false);
            }
        });
        $('#invoice_discount_percent').keyup(function () {
            if (this.value.length > 0) {
                $('#invoice_discount_amount').prop('disabled', true);
            } else {
                $('#invoice_discount_amount').prop('disabled', false);
            }
        });
@php
} @endphp

@php if ($invoice->invoice_is_recurring) {
    @endphp
        $(document).on('click', '.js-item-recurrence-toggler', function () {
            var itemRecurrenceState = $(this).next('input').val();
            if (itemRecurrenceState === ('1')) {
                $(this).next('input').val('0');
                $(this).removeClass('fa-calendar-check-o text-success');
                $(this).addClass('fa-calendar-o text-muted');
            } else {
                $(this).next('input').val('1');
                $(this).removeClass('fa-calendar-o text-muted');
                $(this).addClass('fa-calendar-check-o text-success');
            }
        });
@php
} @endphp

    });
</script>

{{ $modal_delete_invoice;
echo $legacy_calculation ? $modal_add_invoice_tax : '';
// Legacy calculation have global taxes - since v1.6.3
?>
<div id="headerbar">
    <h1 class="headerbar-title">
        <span data-toggle="tooltip" data-placement="bottom" title="@php @@lang('invoicing') }}: <?php
_htmlsc(PHP_EOL . format_user($invoice->user_id)) }}">
            {{ trans('invoice') . ' ' . ($invoice->invoice_number ? '#' . $invoice->invoice_number : trans('id') . ': ' . $invoice->invoice_id) }}
        </span>
@php // Nb Admins > 1 only
if ($change_user) {
    @endphp
        <a data-toggle="tooltip" data-placement="bottom"
           title="{{ $edit_user_title }}"
           href="{{ url('users/form/' . $invoice->user_id) }}">
            <i class="fa fa-xs fa-user text-{{ $my_class }}"></i>
                <span class="hidden-xs">@php
    _htmlsc($invoice->user_name);
    @endphp</span>
        </a>
@php
    if ($invoice->invoice_status_id == 1 && !$invoice->creditinvoice_parent_id) {
        @endphp

        <span id="invoice_change_user" class="fa fa-fw fa-edit text-{{ $its_mine ? 'muted' : 'danger' }} cursor-pointer"
              data-toggle="tooltip" data-placement="bottom"
              title="@@lang('change_user')"></span>
@php
    }
    // End if draft
}
// End if change_user @endphp
    </h1>

    <div class="headerbar-item pull-right{{ $invoice->is_read_only != 1 || $invoice->invoice_status_id != 4 ? ' btn-group' : '' }}">

        <div class="options btn-group btn-group-sm">
            <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-caret-down no-margin"></i> @@lang('options')
            </a>
            <ul class="dropdown-menu">
@php if ($legacy_calculation && $invoice->is_read_only != 1) {
    // Legacy calculation have global taxes - since v1.6.3
    @endphp
                <li>
                    <a href="#add-invoice-tax" data-toggle="modal">
                        <i class="fa fa-plus fa-margin"></i> @@lang('add_invoice_tax')
                    </a>
                </li>
@php
}
// End if @endphp
                <li>
                    <a href="#" id="btn_create_credit"
                       data-invoice-id="{{ $invoice_id }}">
                        <i class="fa fa-minus fa-margin"></i> @@lang('create_credit_invoice')
                    </a>
                </li>
@php if ($invoice->invoice_balance != 0) {
    @endphp
                <li>
                    <a href="#" class="invoice-add-payment"
                       data-invoice-id="{{ $invoice_id }}"
                       data-invoice-balance="{{ $invoice->invoice_balance }}"
                       data-invoice-payment-method="{{ $invoice->payment_method }}"
                       data-payment-cf-exist="{{ $payment_cf_exist ?? '' }}">
                        <i class="fa fa-credit-card fa-margin"></i>
                        @@lang('enter_payment')
                    </a>
                </li>
@php
} @endphp
                <li>
                    <a href="#" id="btn_generate_pdf"
                       data-invoice-id="{{ $invoice_id }}">
                        <i class="fa fa-print fa-margin"></i>
                        @@lang('download_pdf')
                    </a>
                </li>
@php // eInvoice & user fields OK: Show download XML Option
if ($einvoice->user) {
    @endphp
                <li>
                    <a href="#" id="btn_generate_xml"
                       data-invoice-id="{{ $invoice_id }}">
                        <i class="fa fa-file-code-o fa-margin"></i>
                        @@lang('download_xml')
                    </a>
                </li>
@php
} @endphp
                <li>
                    <a href="{{ url('mailer/invoice/' . $invoice->invoice_id) }}">
                        <i class="fa fa-send fa-margin"></i>
                        @@lang('send_email')
                    </a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="#" id="btn_create_recurring"
                       data-invoice-id="{{ $invoice_id }}">
                        <i class="fa fa-refresh fa-margin"></i>
                        @@lang('create_recurring')
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_copy_invoice"
                       data-invoice-id="{{ $invoice_id }}"
                       data-client-id="{{ $invoice->client_id }}">
                        <i class="fa fa-copy fa-margin"></i>
                        @@lang('copy_invoice')
                    </a>
                </li>
@php if ($invoice->invoice_status_id == 1 || $this->config->item('enable_invoice_deletion') === true && $invoice->is_read_only != 1) {
    @endphp
                <li>
                    <a href="#delete-invoice" data-toggle="modal">
                        <i class="fa fa-trash-o fa-margin"></i>
                        @@lang('delete')
                    </a>
                </li>
@php
}
// End if @endphp
            </ul>
        </div>

@php if ($invoice->is_read_only != 1 || $invoice->invoice_status_id != 4) {
    @endphp
        <a href="#" class="btn btn-sm btn-success ajax-loader" id="btn_save_invoice">
            <i class="fa fa-check"></i> @@lang('save')
        </a>
@php
}
//End if @endphp
    </div>

    <div class="headerbar-item invoice-labels pull-right">
@php if ($invoice->invoice_is_recurring) {
    @endphp
        <span class="label label-info">
            <i class="fa fa-refresh"></i> @@lang('recurring')
        </span>
@php
}
if ($invoice->is_read_only == 1) {
    @endphp
        <span class="label label-danger">
            <i class="fa fa-read-only"></i> @@lang('read_only')
        </span>
@php
} @endphp
    </div>

</div>

<div id="content">

    {{ $this->layout->loadView('layout/alerts') }}

    <div id="invoice_form">
        <div class="invoice">

            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-5">

                    <h2>
                        <a href="{{ url('clients/view/' . $invoice->client_id) }}">@php _htmlsc(format_client($invoice)); @endphp</a>
@php if ($invoice->invoice_status_id == 1 && !$invoice->creditinvoice_parent_id) {
    @endphp
                        <span id="invoice_change_client" class="fa fa-edit cursor-pointer small"
                              data-toggle="tooltip" data-placement="bottom"
                              title="@@lang('change_client')"></span>
@php
}
// End if @endphp
                    </h2>
                    <br>
                    <div class="client-address">
                        @php $this->layout->loadView('clients/partial_client_address', ['client' => $invoice]); @endphp
                    </div>
@php if ($invoice->client_phone || $invoice->client_email) {
    @endphp
                    <hr>
@php
}
if ($invoice->client_phone) {
    @endphp
                    <div>@@lang('phone'):&nbsp;@php
    _htmlsc($invoice->client_phone);
    @endphp</div>
@php
}
if ($invoice->client_email) {
    @endphp
                    <div>@@lang('email'):&nbsp;@php
    _auto_link($invoice->client_email);
    @endphp</div>
@php
} @endphp

                </div>

                <div class="col-xs-12 visible-xs"><br></div>

                <div class="col-xs-12 col-sm-5 col-sm-offset-1 col-md-6 col-md-offset-1">
                    <div class="details-box panel panel-default panel-body">
                        <div class="row">
@php if ($invoice->invoice_sign == -1) {
    $parent_invoice_number = $this->mdl_invoices->getParentInvoiceNumber($invoice->creditinvoice_parent_id);
    $view_link = anchor('/invoices/view/' . $invoice->creditinvoice_parent_id, trans('credit_invoice_for_invoice') . ' ' . $parent_invoice_number);
    @endphp
                            <div class="col-xs-12">
                                <div class="alert alert-warning small">
                                    <i class="fa fa-credit-invoice"></i>&nbsp;{{ $view_link }}
                                </div>
                            </div>
@php
}
// End if @endphp

                            <div class="col-xs-12 col-md-6">

                                <div class="invoice-properties">
@php if ($einvoice->name) {
    @endphp
                                    <label class="pull-right" id="e_invoice_active"
                                           data-toggle="tooltip" data-placement="bottom"
                                           title="e-{{ trans('invoice') . ' ' . ($einvoice->user ? trans('version') . ' ' . $einvoice->name . ' 🗸' : '🚫 ' . trans('einvoicing_user_fields_error')) }}"
                                    >
                                        <i class="fa fa-file-code-o"></i>
                                        {{ $einvoice->name;
    if ($einvoice->user) {
        @endphp
                                        <i class="fa fa-check-square-o text-success"></i>
@php
    } else {
        @endphp
                                        <a class="fa fa-user-times text-warning"
                                           href="{{ url('users/form/' . $invoice->user_id) }}"
                                           data-toggle="tooltip" data-placement="top"
                                           title="{{ $edit_user_title }}"
                                        ></a>
@php
    }
    @endphp

                                    </label>
@php
} @endphp
                                    <label for="invoice_number">@php @@lang('invoice') }} #</label>
                                    <input type="text" id="invoice_number" class="form-control"
@php if ($invoice->invoice_number) {
    @endphp
                                           value="{{ $invoice->invoice_number }}"
@php
} else {
    @endphp
                                           placeholder="@@lang('not_set')"
@php
} @endphp
                                           {{ $invoice->is_read_only ? 'disabled="disabled"' : '' }}
                                    >

                                </div>

                                <div class="invoice-properties has-feedback">
                                    <label>@@lang('date')</label>

                                    <div class="input-group">
                                        <input name="invoice_date_created" id="invoice_date_created"
                                               class="form-control datepicker"
                                               value="{{ date_from_mysql($invoice->invoice_date_created) }}"
                                               {{ $invoice->is_read_only ? 'disabled="disabled"' : '' }}>
                                        <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                                    </div>
                                </div>

                                <div class="invoice-properties has-feedback">
                                    <label>@@lang('due_date')</label>

                                    <div class="input-group">
                                        <input name="invoice_date_due" id="invoice_date_due"
                                               class="form-control datepicker"
                                               value="{{ date_from_mysql($invoice->invoice_date_due) }}"
                                               {{ $invoice->is_read_only ? 'disabled="disabled"' : '' }}>
                                        <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 col-md-6">

                                <div class="invoice-properties">
                                    <label>
                                        @php @@lang('status');
if ($invoice->is_read_only != 1 || $invoice->invoice_status_id != 4) {
    echo ' <span class="small">(' . trans('can_be_changed') . ')</span>';
} @endphp
                                    </label>
                                    <select name="invoice_status_id" id="invoice_status_id"
                                            class="form-control simple-select" data-minimum-results-for-search="Infinity"
                                            {{ $invoice->is_read_only == 1 && $invoice->invoice_status_id == 4 ? 'disabled="disabled"' : '' }}
                                    >
@php foreach ($invoice_statuses as $key => $status) {
    $is_selected = $key == $invoice->invoice_status_id ? ' selected="selected"' : '';
    @endphp
                                        <option value="{{ $key }}"{{ $is_selected }}>
                                            {{ $status['label'] }}
                                        </option>
@php
} @endphp
                                    </select>
                                </div>

                                <div class="invoice-properties">
                                    <label>@@lang('payment_method')</label>
                                    <select name="payment_method" id="payment_method"
                                            class="form-control simple-select"
                                            {{ $invoice->is_read_only == 1 && $invoice->invoice_status_id == 4 ? 'disabled="disabled"' : '' }}
                                    >
                                        <option value="0">@@lang('select_payment_method')</option>
@php foreach ($payment_methods as $payment_method) {
    @endphp
                                        <option @php
    check_select($invoice->payment_method, $payment_method->payment_method_id);
    @endphp
                                            value="{{ $payment_method->payment_method_id }}">
                                            {{ $payment_method->payment_method_name }}
                                        </option>
@php
}
// End foreach @endphp
                                    </select>
                                </div>

                                <div class="invoice-properties">
                                    <label>@@lang('invoice_password')</label>
                                    <input type="text" id="invoice_password" class="form-control"
                                           value="@php _htmlsc($invoice->invoice_password); @endphp"
                                           {{ $invoice->is_read_only ? 'disabled="disabled"' : '' }}>
                                </div>
                            </div>

@php $default_custom = false;
$classes = ['control-label', 'controls', '', 'col-xs-12 col-md-6'];
foreach ($custom_fields as $custom_field) {
    if (!$default_custom && !$custom_field->custom_field_location) {
        $default_custom = true;
    }
    if ($custom_field->custom_field_location == 1) {
        print_field($this->mdl_invoices, $custom_field, $custom_values, $classes[0], $classes[1], $classes[2], $classes[3]);
    }
} @endphp

@php if ($invoice->invoice_status_id != 1) {
    @endphp
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label for="invoice-guest-url">@@lang('guest_url')</label>
                                    <div class="input-group">
                                        <input type="text" id="invoice-guest-url" readonly class="form-control"
                                               value="{{ url('guest/view/invoice/' . $invoice->invoice_url_key) }}">
                                        <span class="input-group-addon to-clipboard cursor-pointer"
                                              data-clipboard-target="#invoice-guest-url">
                                            <i class="fa fa-clipboard fa-fw"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
@php
}
// End if @endphp

                        </div>
                    </div>
                </div>

            </div>

            <br>

@php $this->layout->loadView('invoices/partial_itemlist_' . (get_setting('show_responsive_itemlist') ? 'responsive' : 'table')); @endphp

            <hr>

            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="panel panel-default no-margin">
                        <div class="panel-heading">
                            @@lang('invoice_terms')
                        </div>
                        <div class="panel-body">
                            <textarea id="invoice_terms" name="invoice_terms" class="form-control" rows="3"
                                      {{ $invoice->is_read_only ? 'disabled="disabled"' : '' }}
                            >@php _htmlsc($invoice->invoice_terms); @endphp</textarea>
                        </div>
                    </div>

                    <div class="col-xs-12 visible-xs visible-sm"><br></div>

                </div>
                <div class="col-xs-12 col-md-6">

                    @php _dropzone_html($invoice->is_read_only); @endphp

                </div>
            </div>

@php if ($default_custom) {
    @endphp
            <div class="row">
                <div class="col-xs-12">

                    <hr>

                    <div class="panel panel-default">
                        <div class="panel-heading">@@lang('custom_fields')</div>
                        <div class="panel-body">
                            <div class="row">
@php
    $classes = ['control-label', 'controls', '', 'form-group col-xs-12 col-sm-6'];
    foreach ($custom_fields as $custom_field) {
        if (!$custom_field->custom_field_location) {
            // == 0
            print_field($this->mdl_invoices, $custom_field, $custom_values, $classes[0], $classes[1], $classes[2], $classes[3]);
        }
    }
    @endphp
                            </div>
                        </div>
                    </div>

                </div>
            </div>
<?php
}
// End if custom_fields @endphp

        </div>
    </div>
</div>

<?php
_dropzone_script($invoice->invoice_url_key, $invoice->client_id);