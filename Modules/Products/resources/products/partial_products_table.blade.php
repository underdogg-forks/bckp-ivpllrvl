<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

        <thead>
        <tr>
            <th>@lang('family')</th>
            <th>@lang('product_sku')</th>
            <th>@lang('product_name')</th>
            <th>@lang('product_description')</th>
            <th class="amount last">@lang('product_price')</th>
            <th>@lang('product_unit')</th>
            <th>@lang('tax_rate')</th>
            @if(get_setting('sumex') == '1')
                <th>@lang('product_tariff')</th>
            @endif
            <th>@lang('options')</th>
        </tr>
        </thead>

        <tbody>
        @foreach($products as $product)
            <tr>
                <td><a href="{{ url('families/form/' . $product->family_id) " }}><i
                            class="fa fa-edit"></i> {!! $product->family_name !!}</a></td>
                <td>{!! $product->product_sku !!}</td>
                <td><a href="{{ url('products/form/' . $product->product_id) " }}><i
                            class="fa fa-edit"></i> {!! $product->product_name !!}</a></td>
                <td>{{ nl2br(e($product->product_description)) }}</td>
                <td class="amount last">{{ format_currency($product->product_price) }}</td>
                <td>{!! $product->unit_name !!}</td>
                <td>{{ $product->tax_rate_id ? htmlsc($product->tax_rate_name) : trans('none') }}</td>
                @if(get_setting('sumex') == '1')
                    <td>{!! $product->product_tariff !!}</td>
                @endif
                <td>
                    <div class="options inline-flex rounded-md shadow-sm">
                        <a class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5"
                           data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> @lang('options')
                        </a>
                        <ul class="absolute z-10 mt-2 min-w-[160px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg overflow-hidden">
                            <li>
                                <a href="{{ url('products/form/' . $product->product_id) " }}>
                                    <i class="fa fa-edit fa-margin"></i> @lang('edit')
                                </a>
                            </li>
                            <li>
                                <form action="{{ url('products/delete/' . $product->product_id) }}"
                                      method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                            onclick="return confirm('@lang('delete_record_warning')');">
                                        <i class="fa fa-trash-o fa-margin"></i> @lang('delete')
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>

    </table>
</div>
