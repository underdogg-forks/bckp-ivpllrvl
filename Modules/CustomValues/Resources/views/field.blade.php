@php
    $href = url('custom_fields/form/' . $field->custom_field_id);
    $link = '<a href="' . $href . '" class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"><i class="fa fa-edit fa-margin"></i> ' . e($field->custom_field_label) . '</a>';
    $alpha = strtr(mb_strtolower($field->custom_field_type), ['-' => '_']);
    $table = strtr($field->custom_field_table, ['ip_' => '', '_custom' => '']);
@endphp
<div id="headerbar">
    <h1 class="headerbar-title">@lang('custom_values')</h1>
    <div class="headerbar-item float-right">
        <div class="inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm">
            <a class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" href="{{ url('custom_values') " }}>
                <i class="fa fa-arrow-left"></i> @lang('back')
            </a>
            <a class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" href="{{ url('custom_values/create/' . $id) " }}>
                <i class="fa fa-plus"></i> @lang('new')
            </a>
        </div>
    </div>
    <div class="hidden sm:block md:hidden md:block lg:hidden lg:block headerbar-item float-right">
        <div class="badge">@lang('table'): {{ $table }}</div>
        <div class="badge">@lang('position'): {{ $position }}</div>
        <div class="badge">@lang('type'): {{ $alpha }}</div>
        @lang('field'): {!! $link !!}
    </div>
</div>
<div id="content">
    @include('layout.alerts')
    <div class="flex flex-wrap -mx-4">
        <div class="w-full px-4 md:w-1/2 col-md-offset-3">
            <div class="mb-4">
                <div id="filter_results">
                    @include('custom_values.partial_custom_values_field')
                </div>
            </div>

            <div class="flex flex-wrap -mx-4 block sm:hidden">
                <div class="w-full px-4">
                    <div class="mb-4">@lang('field'): {{ $link }}</div>
                </div>

                <div class="w-full px-4">
                    <div class="mb-4 badge">@lang('table'): @php _trans($table)</div>
                </div>

                <div class="w-full px-4">
                    <div class="mb-4 badge">@lang('position'): {{ $position }}</div>
                </div>

                <div class="w-full px-4">
                    <div class="mb-4 badge">@lang('type'): @php _trans($alpha)</div>
                </div>
            </div>

        </div>

        @include('layout/partial/custom_field_usage_list', ['custom_field_table' => $field->custom_field_table])

    </div>
</div>
