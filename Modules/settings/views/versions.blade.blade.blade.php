@php namespace Modules\Settings\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@@lang('version_history')</h1>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('settings/versions/index'), 'mdl_versions') }}
    </div>
</div>

<div id="content" class="table-content">

    <div class="table-responsive">
        <table class="table">

            <thead>
            <tr>
                <th>@@lang('date_applied')</th>
                <th>@@lang('sql_file')</th>
                <th>@@lang('errors')</th>
            </tr>
            </thead>

            <tbody>
            @php foreach ($versions as $version) {
    @endphp
                <tr>
                    <td>{{ date_from_timestamp($version->version_date_applied) }}</td>
                    <td>{{ $version->version_file }}</td>
                    <td>{{ $version->version_sql_errors }}</td>
                </tr>
            <?php
} @endphp
            </tbody>

        </table>
    </div>

</div>

<?php 