@php namespace Modules\Products\Views; @endphp
<div class="table-responsive">
    <table id="products_table" class="table table-hover table-bordered table-striped">
        <tr>
            <th>&nbsp;</th>
            <th>@@lang('product_sku')</th>
            <th>@@lang('family_name')</th>
            <th>@@lang('product_name')</th>
            <th>@@lang('product_description')</th>
            <th class="amount">@@lang('product_price')</th>
        </tr>
        @php foreach ($products as $product) {
    @endphp
            <tr class="product">
                <td class="text-left">
                    <input type="checkbox" name="product_ids[]"
                           value="{{ $product->product_id }}">
                </td>
                <td nowrap class="text-left">
                    <b>@php
    _htmlsc($product->product_sku);
    @endphp</b>
                </td>
                <td>
                    <b>@php
    _htmlsc($product->family_name);
    @endphp</b>
                </td>
                <td>
                    <b>@php
    _htmlsc($product->product_name);
    @endphp</b>
                </td>
                <td>
                    {{ nl2br(htmlsc($product->product_description)) }}
                </td>
                <td class="amount">
                    {{ format_currency($product->product_price) }}
                </td>
            </tr>
        <?php
} @endphp

    </table>
</div>
<?php 
