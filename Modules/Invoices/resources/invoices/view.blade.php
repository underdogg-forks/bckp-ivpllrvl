@php
    if(config('disable_read_only') === true) {
        $invoice->is_read_only = 0;
    }
    $its_mine = $this->session->__get('user_id') == $invoice->user_id;
    $my_class = $its_mine ? 'success' : 'warning';
    $edit_user_title = trans('edit') . ' ' . trans('user') . ' (' . trans('invoicing') . '): ' . PHP_EOL . htmlsc(format_user($invoice->user_id));
@endphp

<script>
    $(function() {
        $('.item-task-id').each(function() {
            if ($(this).val().length > 0) {
                $('#invoice_change_client').hide();
                return false;
            }
        });

        $('.btn_add_product').click(function() {
            $('#modal-placeholder').load("{{ url('products/ajax/modal_product_lookups') }}/" + Math.floor(Math.random() * 1000));
        });

        $('.btn_add_task').click(function() {
            $('#modal-placeholder').load("{{ url('tasks/ajax/modal_task_lookups/' . $invoice_id) }}/" + Math.floor(Math.random() * 1000));
        });

        $('.btn_add_row').click(function() {
            $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
            check_items_tax_usages();
        });

        @if(!$items)
        $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        @endif

        $(document).on('loaded', check_items_tax_usages());

        $('#btn_create_recurring').click(function() {
            $('#modal-placeholder').load("{{ url('invoices/ajax/modal_create_recurring') }}", {
                invoice_id: {{ $invoice_id }}
            });
        });

        @if($invoice->invoice_status_id == 1 && !$invoice->creditinvoice_parent_id)
        $('#invoice_change_client').click(function() {
            $('#modal-placeholder').load("{{ site_url('invoices/ajax/modal_change_client') }}", {
                invoice_id: {{ $invoice_id }},
                client_id: "{{ $this->db->escape_str($invoice->client_id) }}"
            });
        });

        $('#invoice_change_user').click(function() {
            $('#modal-placeholder').load("{{ url('invoices/ajax/modal_change_user') }}", {
                invoice_id: {{ $invoice_id }},
                user_id: "{{ $this->db->escape_str($invoice->user_id) }}"
            });
        });
        @endif

        $('#btn_save_invoice').click(function() {
            var items = [];
            var item_order = 1;
            $('#item_table .item').each(function() {
                var row = {};
                $(this).find('input,select,textarea').each(function() {
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
                payment_method: $('#payment_method').val()
            }, function(data) {
                var response = json_parse(data, {{ (int) IP_DEBUG }});
                if (response.success === 1) {
                    window.location = "{{ url('invoices/view/' . $invoice_id) }}";
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
                    $('#invoice_form').prepend('<div class="p-4 mb-4 text-red-700 dark:text-red-200 bg-red-100 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg">' + all_resp_errors + '</div>');
                }
            });
        });

        $('#btn_generate_pdf').click(function() {
            window.open('{{ url('invoices/generate_pdf/' . $invoice_id) }}', '_blank');
        });

        $('#btn_generate_xml').click(function() {
            window.open('{{ url('invoices/generate_xml/' . $invoice_id) }}', '_blank');
        });

        $(document).on('click', '.btn_delete_item', function() {
            var btn = $(this);
            var item_id = btn.data('item-id');
            if (typeof item_id === 'undefined') {
                $(this).parents('.item').remove();
                check_items_tax_usages();
            } else {
                $.post("{{ url('invoices/ajax/delete_item/' . $invoice->invoice_id) }}", {
                    item_id: item_id
                }, function(data) {
                    var response = json_parse(data, {{ (int) IP_DEBUG }});
                    if (response.success === 1) {
                        btn.parents('.item').remove();
                    } else {
                        btn.removeClass('btn-link').addClass('btn-danger').prop('disabled', true);
                    }
                    check_items_tax_usages();
                });
            }
        });

        @if($invoice->is_read_only != 1)
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

        $(document).on('click', '.up', function() {
            UpR($(this));
        });
        $(document).on('click', '.down', function() {
            DownR($(this));
        });
        @else
        var fixHelper = function(e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index) {
                $(this).width($originals.eq(index).width());
            });
            return $helper;
        };

        $('#item_table').sortable({
            items: 'tbody',
            helper: fixHelper
        });
        @endif

        $('#invoice_discount_amount').keyup(function() {
            if (this.value.length > 0) {
                $('#invoice_discount_percent').prop('disabled', true);
            } else {
                $('#invoice_discount_percent').prop('disabled', false);
            }
        });

        $('#invoice_discount_percent').keyup(function() {
            if (this.value.length > 0) {
                $('#invoice_discount_amount').prop('disabled', true);
            } else {
                $('#invoice_discount_amount').prop('disabled', false);
            }
        });
        @endif

        @if($invoice->invoice_is_recurring)
        $(document).on('click', '.js-item-recurrence-toggler', function() {
            var itemRecurrenceState = $(this).next('input').val();
            if (itemRecurrenceState === '1') {
                $(this).next('input').val('0');
                $(this).removeClass('fa-calendar-check-o text-success').addClass('fa-calendar-o text-muted');
            } else {
                $(this).next('input').val('1');
                $(this).removeClass('fa-calendar-o text-muted').addClass('fa-calendar-check-o text-success');
            }
        });
        @endif
    });
</script>

{!! $modal_delete_invoice !!}
@if($legacy_calculation)
    {!! $modal_add_invoice_tax !!}
@endif

<div id="headerbar">
    <h1 class="headerbar-title">
        <span data-toggle="tooltip" data-placement="bottom"
              title="{{ trans('invoicing') }}: {{ htmlspecialchars(PHP_EOL . format_user($invoice->user_id)) }}">
            {{ trans('invoice') }} {{ $invoice->invoice_number ? '#' . $invoice->invoice_number : trans('id') . ': ' . $invoice->invoice_id }}
        </span>

        @if($change_user)
            <a data-toggle="tooltip" data-placement="bottom" title="{{ $edit_user_title }}"
               href="{{ url('users/form/' . $invoice->user_id) }}">
                <i class="fa fa-xs fa-user text-{{ $my_class }}"></i>
                <span class="hidden sm:block">{!! $invoice->user_name !!}</span>
            </a>

            @if($invoice->invoice_status_id == 1 && !$invoice->creditinvoice_parent_id)
                <span id="invoice_change_user"
                      class="fa fa-fw fa-edit text-{{ $its_mine ? 'muted' : 'danger' }} cursor-pointer"
                      data-toggle="tooltip" data-placement="bottom" title="@lang('change_user')"></span>
            @endif
        @endif
    </h1>
</div>

<div id="content">
    {{ $this->layout->loadView('layout/alerts') }}

    <div id="invoice_form">
        <div class="invoice">
            {{-- Include the rest of your Blade view with corrected @if/@foreach and syntax --}}
        </div>
    </div>
</div>

@php
    _dropzone_script($invoice->invoice_url_key, $invoice->client_id);
@endphp
