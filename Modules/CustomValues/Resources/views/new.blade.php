@php
    $href = url('custom_fields/form/' . $field->custom_field_id);
    $link = '<a href="' . $href . '" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"><i class="fa fa-edit fa-margin"></i> ' . e($field->custom_field_label) . '</a>';
    $alpha = strtr(mb_strtolower($field->custom_field_type), ['-' => '_']);
    $table = strtr($field->custom_field_table, ['ip_' => '', '_custom' => '']);
@endphp
<form method="post">
    @csrf
    <div id="headerbar">
        <h1 class="headerbar-title">@lang('custom_values_new')</h1>
        @include('layout.header_buttons')
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
                    <label for="custom_values_value">@lang('value'):</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors" name="custom_values_value" id="custom_values_value"
                           required>
                </div>
                <div class="flex flex-wrap -mx-4 block sm:hidden">
                    <div class="w-full px-4">
                        <div class="mb-4">@lang('field'): {!! $link !!}</div>
                    </div>
                    <div class="w-full px-4">
                        <div class="mb-4 badge">@lang('table'): {{ $table }}</div>
                    </div>
                    <div class="w-full px-4">
                        <div class="mb-4 badge">@lang('position'): {{ $position }}</div>
                    </div>
                    <div class="w-full px-4">
                        <div class="mb-4 badge">@lang('type'): {{ $alpha }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
