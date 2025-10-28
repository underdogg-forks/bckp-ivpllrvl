
<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>

        <h2>@lang('setup_complete')</h2>

        <p>
            @lang('setup_complete_message')
        </p>

        <p class="p-4 mb-4 text-cyan-700 dark:text-cyan-200 bg-cyan-100 dark:bg-cyan-900/50 border border-cyan-200 dark:border-cyan-800 rounded-lg">
            @lang('setup_complete_support_note')
        </p>

        <p class="p-4 mb-4 text-yellow-700 dark:text-yellow-200 bg-yellow-100 dark:bg-yellow-900/50 border border-yellow-200 dark:border-yellow-800 rounded-lg">
            @lang('setup_complete_secure_setup')
        </p>

        @if($this->session->userdata('setup_notice')) {
    $setup_notice = $this->session->userdata('setup_notice')
        <div class="alert {{ $setup_notice['type']" }}>
            {{ $setup_notice['content'] }}
        </div>
            @endif

        <a href="{{ url('sessions/login') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
            <i class="fa fa-check fa-margin"></i> @lang('login')
        </a>

    </div>
</div>
