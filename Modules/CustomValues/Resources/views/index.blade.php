@php namespace Modules\CustomValues\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@lang('custom_values')</h1>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
            <a class="btn btn-default" href="{{ url('custom_fields') }}">
                <i class="fa fa-arrow-left"></i> @lang('back')
            </a>
            <a class="btn btn-primary" href="{{ url('custom_fields/form') }}">
                <i class="fa fa-plus"></i> @lang('new')
            </a>
        </div>
    </div>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('custom_values/index'), 'mdl_custom_values') }}
    </div>
</div>

<div id="content" class="table-content">

    {{ $this->layout->loadView('layout/alerts') }}

    <div id="filter_results">
        @include('custom_values.partial_custom_values_table')
    </div>

</div>
