
<div class="table-responsive">
    <table class="table table-hover table-striped">

        <thead>
        <tr>
            <th>@lang('family')</th>
            <th>@lang('product_sku')</th>
            <th>@lang('product_name')</th>
            <th>@lang('product_description')</th>
            <th class="amount last">@lang('product_price')</th>
            <th>@lang('product_unit')</th>
            <th>@lang('tax_rate')</th>
            @php $sumex_active = get_setting('sumex') == '1';
if ($sumex_active) {
            @endphp
            <th>@lang('product_tariff')</th>
            @endif
            <th>@lang('options')</th>
        </tr>
        </thead>

        <tbody>
        @foreach($products as $product)
        <tr>
            <td><a href="{{ url('families/form/' . $product->family_id) }}"><i class="fa fa-edit"></i> {!! $product->family_name !!}</a></td>
            <td>{!! $product->product_sku !!}</td>
            <td><a href="{{ url('products/form/' . $product->product_id) }}"><i class="fa fa-edit"></i> {!! $product->product_name !!}</a></td>
            <td>{{ nl2br(e($product->product_description)) }}</td>
            <td class="amount last">{{ format_currency($product->product_price) }}</td>
            <td>{!! $product->unit_name !!}</td>
            <td>{{ $product->tax_rate_id ? htmlsc($product->tax_rate_name) : trans('none') }}</td>
            @if($sumex_active)
            <td>{!! $product->product_tariff !!}</td>
            @php
                @endif
            <td>
                <div class="options btn-group">
                    <a class="btn btn-default btn-sm dropdown-toggle"
                       data-toggle="dropdown" href="#">
                        <i class="fa fa-cog"></i> @lang('options')
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ url('products/form/' . $product->product_id) }}">
                                <i class="fa fa-edit fa-margin"></i> @lang('edit')
                            </a>
                        </li>
                        <li>
                            <form action="{{ url('products/delete/' . $product->product_id) }}"
                                  method="POST">
                                @csrf
                                <button type="submit" class="dropdown-button"
                                        onclick="return confirm('@lang('delete_record_warning')');">
                                    <i class="fa fa-trash-o fa-margin"></i> @lang('delete')
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    <?php
@endforeach
</tbody >

        </table >
    </div >
<?php
