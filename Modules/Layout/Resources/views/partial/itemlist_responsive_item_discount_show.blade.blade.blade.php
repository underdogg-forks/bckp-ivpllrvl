@php namespace Modules\Layout\Views\Partial;

// Called in [quotes|invoices]/partial_itemlist_responsive.php (item & new) line
$item_value = isset($item) ? format_currency($item->item_discount) : ''; @endphp
<hr class="no-margin">
<div class="row mb-1">
    <div class="col-xs-9 col-sm-8">@lang('item_discount'):</div>
    <div class="col-xs-3 col-sm-4">{{ $item_value }}</div>
</div>
@php $item_global_discount = $item_value ? $item->item_subtotal - ($item->item_total - $item->item_tax_total + $item->item_discount) : 0;
if (!$legacy_calculation && $item_global_discount) {
@endphp
<div class="row mb-1">
    <div class="col-xs-9 col-sm-8">@lang('global_discount'):</div>
    <div class="col-xs-3 col-sm-4">{{ format_currency($item_global_discount) }}</div>
</div>
<div class="row mb-1">
    <div class="col-xs-9 col-sm-8">@lang('discount') (@lang('subtotal')):</div>
    <div class="col-xs-3 col-sm-4">{{ format_currency($item_global_discount + $item->item_discount) }}</div>
</div>
    @endif
<hr class="no-margin"><?php
