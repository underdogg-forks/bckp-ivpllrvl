@php namespace Modules\Quotes\Views; @endphp
<div id="headerbar">

    <h1 class="headerbar-title">@@lang('quotes')</h1>

    <div class="headerbar-item pull-right">
        <button type="button" class="btn btn-default btn-sm submenu-toggle hidden-lg"
                data-toggle="collapse" data-target="#ip-submenu-collapse">
            <i class="fa fa-bars"></i> @@lang('submenu')
        </button>
        <a class="create-quote btn btn-sm btn-primary" href="#">
            <i class="fa fa-plus"></i> @@lang('new')
        </a>
    </div>

    <div class="headerbar-item pull-right visible-lg">
        {{ pager(site_url('quotes/status/' . $this->uri->segment(3)), 'mdl_quotes') }}
    </div>

    <div class="headerbar-item pull-right visible-lg">
        <div class="btn-group btn-group-sm index-options">
            <a href="{{ url('quotes/status/all') }}"
               class="btn {{ $status == 'all' ? 'btn-primary' : 'btn-default' }}">
                @@lang('all')
            </a>
            <a href="{{ url('quotes/status/draft') }}"
               class="btn {{ $status == 'draft' ? 'btn-primary' : 'btn-default' }}">
                @@lang('draft')
            </a>
            <a href="{{ url('quotes/status/sent') }}"
               class="btn {{ $status == 'sent' ? 'btn-primary' : 'btn-default' }}">
                @@lang('sent')
            </a>
            <a href="{{ url('quotes/status/viewed') }}"
               class="btn {{ $status == 'viewed' ? 'btn-primary' : 'btn-default' }}">
                @@lang('viewed')
            </a>
            <a href="{{ url('quotes/status/approved') }}"
               class="btn {{ $status == 'approved' ? 'btn-primary' : 'btn-default' }}">
                @@lang('approved')
            </a>
            <a href="{{ url('quotes/status/rejected') }}"
               class="btn {{ $status == 'rejected' ? 'btn-primary' : 'btn-default' }}">
                @@lang('rejected')
            </a>
            <a href="{{ url('quotes/status/canceled') }}"
               class="btn {{ $status == 'canceled' ? 'btn-primary' : 'btn-default' }}">
                @@lang('canceled')
            </a>
        </div>
    </div>

</div>

<div id="submenu">
    <div class="collapse clearfix" id="ip-submenu-collapse">

        <div class="submenu-row">
            {{ pager(site_url('quotes/status/' . $this->uri->segment(3)), 'mdl_quotes') }}
        </div>

        <div class="submenu-row">
            <div class="btn-group btn-group-sm index-options">
                <a href="{{ url('quotes/status/all') }}"
                   class="btn {{ $status == 'all' ? 'btn-primary' : 'btn-default' }}">
                    @@lang('all')
                </a>
                <a href="{{ url('quotes/status/draft') }}"
                   class="btn {{ $status == 'draft' ? 'btn-primary' : 'btn-default' }}">
                    @@lang('draft')
                </a>
                <a href="{{ url('quotes/status/sent') }}"
                   class="btn {{ $status == 'sent' ? 'btn-primary' : 'btn-default' }}">
                    @@lang('sent')
                </a>
                <a href="{{ url('quotes/status/viewed') }}"
                   class="btn {{ $status == 'viewed' ? 'btn-primary' : 'btn-default' }}">
                    @@lang('viewed')
                </a>
                <a href="{{ url('quotes/status/approved') }}"
                   class="btn {{ $status == 'approved' ? 'btn-primary' : 'btn-default' }}">
                    @@lang('approved')
                </a>
                <a href="{{ url('quotes/status/rejected') }}"
                   class="btn {{ $status == 'rejected' ? 'btn-primary' : 'btn-default' }}">
                    @@lang('rejected')
                </a>
                <a href="{{ url('quotes/status/canceled') }}"
                   class="btn {{ $status == 'canceled' ? 'btn-primary' : 'btn-default' }}">
                    @@lang('canceled')
                </a>
            </div>
        </div>

    </div>
</div>

<div id="content" class="table-content">

    <div id="filter_results">
        @php $this->layout->loadView('quotes/partial_quote_table'); @endphp
    </div>

</div>
