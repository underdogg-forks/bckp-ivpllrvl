@php

    $active = $this->uri->segment(3); @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@@lang('custom_fields')</h1>

                <div class="headerbar-item pull-right">
                    <a class="btn btn-sm btn-primary" href="{{ url('custom_fields/form') }}">
                        <i class="fa fa-plus"></i> @@lang('new')
        </a>
</div>

<div class="headerbar-item pull-right">
    {{ pager(site_url('custom_fields/table/' . $active), 'mdl_custom_fields') }}
</div>

<div class="headerbar-item pull-right visible-lg">
    <div class="btn-group btn-group-sm index-options">
        <a href="{{ url('custom_fields/table/all') }}"
           class="btn {{ $active == 'all' ? 'btn-primary' : 'btn-default' }}">
            @@lang('all')
        </a>
        @php foreach ($custom_tables as $table) {
        @endphp
        <a href="{{ url('custom_fields/table/' . $table) }}"
           class="btn {{ $active == $table ? 'btn-primary' : 'btn-default' }}">
            @php
                _trans($table);
            @endphp
        </a>
        <?php
} @endphp
    </div>
</div>
</div>

<div id="content" class="table-content">

    {{ $this->layout->loadView('layout/alerts') }}

    <div id="filter_results">
        @php $this->layout->loadView('custom_fields/partial_custom_fields_table'); @endphp
    </div>

</div>
<?php
