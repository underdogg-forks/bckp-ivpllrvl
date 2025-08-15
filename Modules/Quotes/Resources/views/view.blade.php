@php namespace Modules\Quotes\Views;

// Little helper
$its_mine = $this->session->__get('user_id') == $quote->user_id;
$my_class = $its_mine ? 'success' : 'warning';
// visual: work with text-* alert-*
// In change user toggle & After eInvoice (name) when user required field missing
$edit_user_title = trans('edit') . ' ' . trans('user') . ' (' . trans('invoicing') . '): ' . PHP_EOL . htmlsc(format_user($quote->user_id));

<script>
    $(function () {
        $('.btn_add_product').click(function () {
            $('#modal-placeholder').load("{{ url('products/ajax/modal_product_lookups');
?>/" + Math.floor(Math.random() * 1000));
        });

        $('.btn_add_row').click(function () {
            $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
            // Legacy:no: check items tax usage is correct (ReLoad on change)
            check_items_tax_usages();
        });

@if(!$items)
        $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
@endif

        // Legacy:no: check items tax usage is correct (Load on change)
        $(document).on('loaded', check_items_tax_usages());
@if($quote->quote_status_id == 1)
    $('#quote_change_client').click(function () {
        $('#modal-placeholder').load("{{ site_url('quotes/ajax/modal_change_client') }}", {
                quote_id: {{ $quote_id }},
                client_id: "{{ $this->db->escape_str($quote->client_id) }}",
            });
        });

        $('#quote_change_user').click(function () {
            $('#modal-placeholder').load("{{ url('quotes/ajax/modal_change_user') }}", {
                quote_id: {{ $quote_id }},
                user_id: "{{ $this->db->escape_str($quote->user_id) }}",
            });
        });
        @php
            }
            // End if

        $('#btn_save_quote').click(function () {
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
            $.post("{{ url('quotes/ajax/save') }}", {
                    legacy_calculation: {{ (int) $legacy_calculation }},
                    quote_id: {{ $quote_id }},
                    quote_number: $('#quote_number').val(),
                    quote_date_created: $('#quote_date_created').val(),
                    quote_date_expires: $('#quote_date_expires').val(),
                    quote_status_id: $('#quote_status_id').val(),
                    quote_password: $('#quote_password').val(),
                    items: JSON.stringify(items),
                    quote_discount_amount: $('#quote_discount_amount').val(),
                    quote_discount_percent: $('#quote_discount_percent').val(),
                    notes: $('#notes').val(),
                    custom: $('input[name^=custom],select[name^=custom]').serializeArray(),
                },
                function (data) {
                    var response = json_parse(data, {{ (int) IP_DEBUG }});
                    if (response.success === 1) {
                        window.location = "{{ url('quotes/view') }}/" + {{ $quote_id }};
                    } else {
                        $('#fullpage-loader').hide();
                        $('.control-group').removeClass('has-error');
                        $('div.alert[class*="alert-"]').remove();
                        var resp_errors = response.validation_errors,
                            all_resp_errors = '';

                        if (typeof (resp_errors) == 'string') {
                            all_resp_errors = resp_errors;
                        } else {
                            for (var key in resp_errors) {
                                $('#' + key).parent().addClass('has-error');
                                all_resp_errors += resp_errors[key];
                            }
                        }

                        $('#quote_form').prepend('<div class="alert alert-danger">' + all_resp_errors + '</div>');
                    }
                });
        });

        $(document).on('click', '.btn_delete_item', function () {
            var btn = $(this);
            var item_id = btn.data('item-id');

            // Just remove the row if no item ID is set (new row)
            if (typeof item_id === 'undefined') {
                $(this).parents('.item').remove();
                check_items_tax_usages();
            } else {
                $.post("{{ url('quotes/ajax/delete_item/' . $quote->quote_id) }}", {
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

        $('#btn_generate_pdf').click(function () {
            window.open('{{ url('quotes/generate_pdf/' . $quote_id) }}', '_blank');
        });

        $(document).ready(function () {
            if ($('#quote_discount_percent').val().length > 0) {
                $('#quote_discount_amount').prop('disabled', true);
            }
            if ($('#quote_discount_amount').val().length > 0) {
                $('#quote_discount_percent').prop('disabled', true);
            }
        });
        $('#quote_discount_amount').keyup(function () {
            if (this.value.length > 0) {
                $('#quote_discount_percent').prop('disabled', true);
            } else {
                $('#quote_discount_percent').prop('disabled', false);
            }
        });
        $('#quote_discount_percent').keyup(function () {
            if (this.value.length > 0) {
                $('#quote_discount_amount').prop('disabled', true);
            } else {
                $('#quote_discount_amount').prop('disabled', false);
            }
        });

        @if(get_setting('show_responsive_itemlist') == 1)
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
        @else
        var fixHelper = function (e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function (index) {
                $(this).width($originals.eq(index).width());
            });
            return $helper;
        };

        $('#item_table').sortable({
            helper: fixHelper,
            items: 'tbody',
        });
        @endif
    });
</script>

{{ $modal_delete_quote;
echo $legacy_calculation ? $modal_add_quote_tax : '';
// Legacy calculation have global taxes - since v1.6.3
?>
<div id="headerbar">
    <h1 class="headerbar-title">
        <span data-toggle="tooltip" data-placement="bottom" title="@lang('invoicing') }}: <?php
                                                                                          htmlspecialchars(PHP_EOL . format_user($quote->user_id)) }
                                                                                          }">
            {{ trans('quote') . ' ' . ($quote->quote_number ? '#' . $quote->quote_number : trans('id') . ': ' . $quote->quote_id) }}
        </span>

@if($change_user)
        <a data-toggle="tooltip" data-placement="bottom"
           title="{{ $edit_user_title }}"
href="{{ url('users/form/' . $quote->user_id) }}">
<i class="fa fa-xs fa-user text-{{ $my_class }}"></i>
<span class="hidden-xs">{!! $quote->user_name !!}</span>
</a>
@if($quote->quote_status_id == 1)
    <span id="quote_change_user" class="fa fa-fw fa-edit text-{{ $its_mine ? 'muted' : 'danger' }} cursor-pointer"
          data-toggle="tooltip" data-placement="bottom"
          title="@lang('change_user')"></span>
    @php
        }
        // End if draft
    }
    // End if change_user
    </h1>

    <div class="headerbar-item pull-right btn-group">
        <div class="options btn-group btn-group-sm">
            <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-caret-down no-margin"></i> @lang('options')
            </a>
            <ul class="dropdown-menu">

@if($legacy_calculation)
                <li>
                    <a href="#add-quote-tax" data-toggle="modal">
                        <i class="fa fa-plus fa-margin"></i>
                        @lang('add_quote_tax')
                    </a>
                </li>
                @endif
                <li>
                    <a href="#" id="btn_generate_pdf"
                       data-quote-id="{{ $quote_id }}">
                        <i class="fa fa-print fa-margin"></i>
                        @lang('download_pdf')
                    </a>
                </li>
                <li>
                    <a href="{{ url('mailer/quote/' . $quote->quote_id) }}">
                        <i class="fa fa-send fa-margin"></i>
                        @lang('send_email')
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_quote_to_invoice"
                       data-quote-id="{{ $quote_id }}">
                        <i class="fa fa-refresh fa-margin"></i>
                        @lang('quote_to_invoice')
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_copy_quote"
                       data-quote-id="{{ $quote_id }}"
                       data-client-id="{{ $quote->client_id }}">
                        <i class="fa fa-copy fa-margin"></i>
                        @lang('copy_quote')
                    </a>
                </li>
                <li>
                    <a href="#delete-quote" data-toggle="modal">
                        <i class="fa fa-trash-o fa-margin"></i> @lang('delete')
                    </a>
                </li>
            </ul>
        </div>

        <a href="#" class="btn btn-success btn-sm ajax-loader" id="btn_save_quote">
            <i class="fa fa-check"></i>
            @lang('save')
        </a>
    </div>

    </div>

    <div id="content">

        {{ $this->layout->loadView('layout/alerts') }}

        <div id="quote_form">
            <div class="quote">

                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-5">

                        <h3>
                            <a href="{{ url('clients/view/' . $quote->client_id) }}">
                                {!! format_client($quote) !!}
                            </a>
                            @if($quote->quote_status_id == 1)
                                <span id="quote_change_client" class="fa fa-edit cursor-pointer small"
                                      data-toggle="tooltip" data-placement="bottom"
                                      title="@lang('change_client')"></span>
                            @endif
                        </h3>
                        <br>
                        <div class="client-address">
                            @include('clients/partial_client_address', ['client' => $quote])
                        </div>
                        @if($quote->client_phone || $quote->client_email)
                            <hr>
                            @php
                                }
                                if ($quote->client_phone) {

                            <div>
                                @lang('phone'):&nbsp;
                                {!! $quote->client_phone !!}
                            </div>
                            @php
                                }
                                if ($quote->client_email) {

                            <div>
                                @lang('email'):&nbsp;
                                @php
                                    _auto_link($quote->client_email)
                            </div>
                        @endif

                    </div>

                    <div class="col-xs-12 visible-xs"><br></div>

                    <div class="col-xs-12 col-sm-5 col-sm-offset-1 col-md-6 col-md-offset-1">
                        <div class="details-box">
                            <div class="row">

                                <div class="col-xs-12 col-md-6">

                                    <div class="quote-properties">
                                        @if($einvoice->name)
                                            <label class="pull-right" id="e_invoice_active"
                                                   data-toggle="tooltip" data-placement="bottom"
                                                   title="e-{{ trans('invoice') . ' ' . ($einvoice->user ? trans('version') . ' ' . $einvoice->name . ' 🗸' : '🚫 ' . trans('einvoicing_user_fields_error')) }}"
                                            >
                                                <i class="fa fa-file-code-o"></i>
                                                {{ $einvoice->name;
            if ($einvoice->user) {

                                                <i class="fa fa-check-square-o text-success"></i>
        @else
                                                <a class="fa fa-user-times text-warning"
                                                   href="{{ url('users/form/' . $quote->user_id) }}"
                                                data-toggle="tooltip" data-placement="top"
                                                title="{{ $edit_user_title }}"
                                                ></a>
                                                @endif

                                            </label>
                                        @endif

                                        <label for="quote_number">@lang('quote') }} #</label>
                                        <input type="text" id="quote_number" class="form-control"
                                               @if($quote->quote_number)
                                                   value="{{ $quote->quote_number }}"
                                               @else
                                                   placeholder="@lang('not_set')"
                                            @endif
                                        >
                                    </div>
                                    <div class="quote-properties has-feedback">
                                        <label for="quote_date_created">
                                            @lang('date')
                                        </label>
                                        <div class="input-group">
                                            <input name="quote_date_created" id="quote_date_created"
                                                   class="form-control datepicker"
                                                   value="{{ date_from_mysql($quote->quote_date_created) }}"/>
                                            <span class="input-group-addon">
                                            <i class="fa fa-calendar fa-fw"></i>
                                        </span>
                                        </div>
                                    </div>
                                    <div class="quote-properties has-feedback">
                                        <label for="quote_date_expires">
                                            @lang('expires')
                                        </label>
                                        <div class="input-group">
                                            <input name="quote_date_expires" id="quote_date_expires"
                                                   class="form-control datepicker"
                                                   value="{{ date_from_mysql($quote->quote_date_expires) }}">
                                            <span class="input-group-addon">
                                            <i class="fa fa-calendar fa-fw"></i>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6">

                                    <div class="quote-properties">
                                        <label for="quote_status_id">
                                            @lang('status')
                                        </label>
                                        <select name="quote_status_id" id="quote_status_id"
                                                class="form-control simple-select"
                                                data-minimum-results-for-search="Infinity">
                                            @foreach($quote_statuses as $key => $status) {
    $is_selected = $key == $quote->quote_status_id ? ' selected="selected"' : '';

                                            <option value="{{ $key }}"{{ $is_selected }}>
                                                {{ $status['label'] }}
                                            </option>@endforeach
                                        </select>
                                    </div>
                                    <div class="quote-properties">
                                        <label for="quote_password">
                                            @lang('quote_password')
                                        </label>
                                        <input type="text" id="quote_password" class="form-control"
                                               value="{!! $quote->quote_password !!}">
                                    </div>

                                    @if($quote->quote_status_id != 1)
                                        <div class="quote-properties">
                                            <label for="quote-guest-url">@lang('guest_url')</label>
                                            <div class="input-group">
                                                <input type="text" id="quote-guest-url" readonly class="form-control"
                                                       value="{{ url('guest/view/quote/' . $quote->quote_url_key) }}">
                                                <span class="input-group-addon to-clipboard cursor-pointer"
                                                      data-clipboard-target="#quote-guest-url">
                                            <i class="fa fa-clipboard fa-fw"></i>
                                        </span>
                                            </div>
                                        </div>@endforeach

                                </div>
                                @php $default_custom = false;
$classes = ['control-label', 'controls', '', 'col-xs-12 col-md-6'];
foreach ($custom_fields as $custom_field) {
    if (!$default_custom && !$custom_field->custom_field_location) {
        $default_custom = true;
    }
    if ($custom_field->custom_field_location == 1) {
        print_field($this->mdl_quotes, $custom_field, $custom_values, $classes[0], $classes[1], $classes[2], $classes[3]);
    }
}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            @php $this->layout->loadView('quotes/partial_itemlist_' . (get_setting('show_responsive_itemlist') ? 'responsive' : 'table'))

            <hr/>

            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="panel panel-default no-margin">
                        <div class="panel-heading">@lang('notes')</div>
                        <div class="panel-body">
                        <textarea name="notes" id="notes" rows="3"
                                  class="form-control">{!! $quote->notes !!}</textarea>
                        </div>
                    </div>

                    <div class="col-xs-12 visible-xs visible-sm"><br></div>

                </div>
                <div class="col-xs-12 col-md-6">

                    @php _dropzone_html(false)

                </div>
            </div>
            @if($default_custom)
                <div class="row">
                    <div class="col-xs-12 col-md-6">

                        <hr>

                        <div class="panel panel-default">
                            <div class="panel-heading">@lang('custom_fields')</div>
                            <div class="panel-body">
                                <div class="row">
                                    @php
                                        $classes = ['control-label', 'controls', '', 'form-group col-xs-12 col-sm-6'];
                                        foreach ($custom_fields as $custom_field) {
                                            if (!$custom_field->custom_field_location) {
                                                // == 0
                                                print_field($this->mdl_quotes, $custom_field, $custom_values, $classes[0], $classes[1], $classes[2], $classes[3]);
                                            }
                                        }

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                }
                // End if custom_fields
        </div>

_dropzone_script($quote->quote_url_key, $quote->client_id);
