<div id="fullpage-loader" style="display: none">
    <div class="loader-content">
        <i id="loader-icon" class="fa fa-cog fa-spin"></i>
        <div id="loader-error" style="display: none">
            @lang('loading_error')<br/>
            <a href="https://wiki.invoiceplane.com/@lang('cldr')/1.0/general/faq"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors px-3 py-1.5" target="_blank">
                <i class="fa fa-support"></i> @lang('loading_error_help')
            </a>
        </div>
    </div>
    <div class="text-right">
        <button type="button" class="fullpage-loader-close inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-link hover:text-link-hover hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary transition-colors tip" aria-label="@lang('close')"
                title="@lang('close')" data-placement="left">
            <span aria-hidden="true"><i class="fa fa-close"></i></span>
        </button>
    </div>
</div>
