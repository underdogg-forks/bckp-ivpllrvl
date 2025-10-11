@php
    $href = url('custom_fields/form/' . $value->custom_field_id);
    $link = '<a href="' . $href . '" class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"><i class="fa fa-edit fa-margin"></i> ' . e($value->custom_field_label) . '</a>';
    $alpha = strtr(mb_strtolower($value->custom_field_type), ['-' => '_']);
    $table = strtr($value->custom_field_table, ['ip_' => '', '_custom' => '']);
@endphp
<form method="post">
    @csrf
    <div id="headerbar">
        <h1 class="headerbar-title">@lang('custom_values_edit')</h1>
        @include('layout.header_buttons')
        <div class="headerbar-item float-right">
            <a href="{{ url('custom_values/field/' . $value->custom_field_id) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fa fa-eye fa-margin"></i> @lang('values')</a>
        </div>
        <div class="hidden sm:block md:hidden md:block lg:hidden lg:block headerbar-item float-right">
            <div class="badge">@lang('table'): {{ $table }}</div>
            <div class="badge">@lang('position'): {{ $position }}</div>
            <div class="badge">@lang('type'): {{ $alpha }}</div>
            @lang('field'): {!! $link !!}
        </div>
    </div>
    <div id="content">
        <div class="flex flex-wrap -mx-4">
            <div class="w-full px-4 md:w-1/2 col-md-offset-3">
                @include('layout.alerts')
                <div class="mb-4">
                    <label for="custom_values_value">@lang('label'):</label>
                    <input type="text" name="custom_values_value" id="custom_values_value" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors"
                           value="{{ $value->custom_values_value }}" required>
                </div>
                <hr>
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
            @include('layout/partial/custom_field_usage_list', ['custom_field_table' => $value->custom_field_table])
        </div>
    </div>
</form>
