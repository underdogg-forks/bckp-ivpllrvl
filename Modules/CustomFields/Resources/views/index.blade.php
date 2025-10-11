@php

    $active = $this->uri->segment(3);
<div id="headerbar">
    <h1 class="headerbar-title">@lang('custom_fields')</h1>

    <div class="headerbar-item float-right">
        <a class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" href="{{ url('custom_fields/form') " }}>
            <i class="fa fa-plus"></i> @lang('new')
        </a>
    </div>

    <div class="headerbar-item float-right">
        {{ pager(site_url('custom_fields/table/' . $active), 'mdl_custom_fields') }}
    </div>

    <div class="headerbar-item float-right hidden lg:block">
        <div class="inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm index-options">
            <a href="{{ url('custom_fields/table/all') }}"
               class="btn {{ $active == 'all' ? 'btn-primary' : '-default'" }}>
                @lang('all')
            </a>
            @foreach($custom_tables as $table)
            <a href="{{ url('custom_fields/table/' . $table) }}"
               class="btn {{ $active == $table ? 'btn-primary' : '-default'" }}>
                @php
                    _trans($table)
            </a>@endforeach
        </div>
    </div>
</div>

<div id="content" class="table-content">

    {{ $this->layout->loadView('layout/alerts') }}

    <div id="filter_results">
        @include('custom_fields.partial_custom_fields_table')
    </div>

</div>
