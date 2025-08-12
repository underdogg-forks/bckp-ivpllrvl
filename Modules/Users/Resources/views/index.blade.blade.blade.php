@php namespace Modules\Users\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@@lang('users')</h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="{{ url('users/form') }}">
            <i class="fa fa-plus"></i> @@lang('new')
        </a>
    </div>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('users/index'), 'mdl_users') }}
    </div>

</div>

<div id="content" class="table-content">

    {{ $this->layout->loadView('layout/alerts') }}

    <div id="filter_results">
        @php $this->layout->loadView('users/partial_users_table'); @endphp
    </div>

</div>
