
<div id="headerbar">

    <h1 class="headerbar-title">@lang('quotes')</h1>

    <div class="headerbar-item float-right">
        {{ pager(site_url('guest/quotes/status/' . $this->uri->segment(3)), 'mdl_quotes') }}
    </div>

    <div class="headerbar-item float-right">
        <div class="inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm index-options">
            <a href="{{ url('guest/quotes/status/open') }}"
               class="btn {{ $status == 'open' ? 'btn-primary' : '-default'" }}>
                @lang('open')
            </a>
            <a href="{{ url('guest/quotes/status/approved') }}"
               class="btn {{ $status == 'approved' ? 'btn-primary' : '-default'" }}>
                @lang('approved')
            </a>
            <a href="{{ url('guest/quotes/status/rejected') }}"
               class="btn {{ $status == 'rejected' ? 'btn-primary' : '-default'" }}>
                @lang('rejected')
            </a>
            <a href="{{ url('guest/quotes/status/viewed') }}"
               class="btn {{ $status == 'viewed' ? 'btn-primary' : '-default'" }}>
                @lang('viewed')
            </a>
            <a href="{{ url('guest/quotes/status/all') }}"
               class="btn {{ $status == 'all' ? 'btn-primary' : '-default'" }}>
                @lang('all')
            </a>
        </div>
    </div>

</div>

<div id="content" class="table-content">

    {{ $this->layout->loadView('layout/alerts') }}

    <div id="filter_results">

        {{ $this->layout->loadView('guest/partial_quotes_table') }}

    </div>

</div>
