<script>
    $(function () {
        // Display the create invoice modal
        $('#modal-choose-items').modal('show');

        $(".simple-select").select2();

        // Creates the invoice
        $('.select-items-confirm').click(function () {
            var product_ids = [];

            $("input[name='product_ids[]']:checked").each(function () {
                product_ids.push(parseInt($(this).val()));
            });
            // No Check No post
            @if(!product_ids.length) return; // todo: why not animate checkboxes

            $.post("{{ url('products/ajax/process_product_selections') }}", {
                product_ids: product_ids
            }, function (data) {
                var items = json_parse(data, {{ (int) IP_DEBUG }});
                for (var key in items) {
                    // Set default tax rate id if empty
                    @if(!items[key].tax_rate_id) items[key].tax_rate_id = '{{ $default_item_tax_rate }}';

                    @if($('#item_table .item:last input[name=item_name]').val() !== '') {
                        $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
                    }

                    var last_item_row = $('#item_table .item:last');

                    last_item_row.find('input[name=item_name]').val(items[key].product_name);
                    last_item_row.find('textarea[name=item_description]').val(items[key].product_description);
                    last_item_row.find('input[name=item_price]').val(items[key].product_price);
                    last_item_row.find('input[name=item_quantity]').val('1');
                    last_item_row.find('select[name=item_tax_rate_id]').val(items[key].tax_rate_id);
                    last_item_row.find('input[name=item_product_id]').val(items[key].product_id);
                    last_item_row.find('select[name=item_product_unit_id]').val(items[key].unit_id);

                    $('#modal-choose-items').modal('hide');
                }

                // Legacy:no: check items tax usage is correct (ReLoad on change) - since 1.6.3
                check_items_tax_usages();
            });
        });

        // Add on rows a click event to Toggle they checkbox
        function addClickTrToggleCheck() {
            $('#products_table tr').click(function (event) {
                @if(event.target.type !== 'checkbox') {
                    $(':checkbox', this).trigger('click');
                }
            });
        }

        addClickTrToggleCheck(); // init row click event ! important

        // Reset the form
        $('#product-reset-button').click(function () {
            var product_table = $('#product-lookup-table');

            product_table.html('<h2 class="text-center"><i class="fa fa-spin fa-spinner"></i></h2>');

            var lookup_url = "{{ url('products/ajax/modal_product_lookups') }}/";
            lookup_url += Math.floor(Math.random() * 1000) + '/?';
            lookup_url += "&reset_table=true";

            // Reload to default & add rows click event
            window.setTimeout(function () {
                product_table.load(lookup_url, addClickTrToggleCheck);
            }, 250);
        });

        // Filter on search button click
        $('#filter-button').click(function () {
            products_filter();
        });

        // Filter on family dropdown change
        $("#filter_family").change(function () {
            products_filter();
        });

        // Filter products
        function products_filter() {
            var filter_family = $('#filter_family').val();
            var filter_product = $('#filter_product').val();
            var product_table = $('#product-lookup-table');

            product_table.html('<h2 class="text-center"><i class="fa fa-spin fa-spinner"></i></h2>');

            var lookup_url = "{{ url('products/ajax/modal_product_lookups') }}/";
            lookup_url += Math.floor(Math.random() * 1000) + '/?';

            @if(filter_family) {
                lookup_url += "&filter_family=" + filter_family;
            }

            @if(filter_product) {
                lookup_url += "&filter_product=" + filter_product;
            }

            // Reload by filtered & add rows click event
            window.setTimeout(function () {
                product_table.load(lookup_url, addClickTrToggleCheck);
            }, 250);
        }

        // Bind enter to product search if search field is focused
        $(document).keypress(function (e) {
            @if(e.which === 13 && $('#filter_product').is(':focus')) {
                $('#filter-button').click();
                return false;
            }
        });
    })
    ;
</script>

<div id="modal-choose-items" class="modal w-full px-4 col-sm-10 col-sm-offset-1"
     role="dialog" aria-labelledby="modal-choose-items" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">@lang('add_product')</h4>
        </div>
        <div class="modal-body">

            <div class="flex flex-wrap gap-4 items-center">
                <div class="mb-4 filter-form">
                    <select name="filter_family" id="filter_family" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select">
                        <option value="">@lang('any_family')</option>
                        @foreach($families as $family)
                            <option value="{{ $family->family_id }}"
                                    @if(isset($filter_family) && $family->family_id == $filter_family)
{ selected="selected"}@endforeach
                            >
                    {!! $family->family_name) }}
                                                </option>@endforeach
                </select>
            </div>
            <div class="mb-4">
                <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors" name="filter_product" id="filter_product"
                       placeholder="@lang('product_name')"
                       value="{{ $filter_product " }}>
            </div>
            <button type="button" id="filter-button"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">@lang('search_product')</button>
            <button type="button" id="product-reset-button" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                @lang('reset')
            </button>
        </div>

        <br/>

        <div id="product-lookup-table">
            @php $this->layout->loadView('products/partial_product_table_modal' !!}
                </div>

            </div>
            <div class="modal-footer">
                <div class="inline-flex rounded-md shadow-sm">
                    <button class="select-items-confirm inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors" type="button">
                        <i class="fa fa-check"></i>
                        @lang('submit')
                    </button>
                    <button class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" type="button" data-dismiss="modal">
                        <i class="fa fa-times"></i>
                        @lang('cancel')
                    </button>
                </div>
            </div>
    </form>

</div>
