@php namespace Modules\Quotes\Views; @endphp
            <tr>
                <td class="td-vert-middle">@@lang('global_discount')</td>
                <td class="clearfix">
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <input id="quote_discount_amount" name="quote_discount_amount"
                                   class="discount-option form-control amount" aria-label="@@lang('global_discount')"
                                   value="{{ format_amount($quote->quote_discount_amount != 0 ? $quote->quote_discount_amount : '') }}">
                            <span class="input-group-addon">{{ get_setting('currency_symbol') }}</span>
                        </div>
                    </div>
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <input id="quote_discount_percent" name="quote_discount_percent" aria-label="@@lang('global_discount') %"
                                   value="{{ format_amount($quote->quote_discount_percent != 0 ? $quote->quote_discount_percent : '') }}"
                                   class="discount-option form-control amount">
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </td>
            </tr>
<?php 