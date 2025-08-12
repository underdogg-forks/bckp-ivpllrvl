@php namespace Modules\Layout\Views\Includes; @endphp
<div id="fullpage-loader" style="display: none">
    <div class="loader-content">
        <i id="loader-icon" class="fa fa-cog fa-spin"></i>
        <div id="loader-error" style="display: none">
            @@lang('loading_error')<br/>
            <a href="https://wiki.invoiceplane.com/@@lang('cldr')/1.0/general/faq"
               class="btn btn-primary btn-sm" target="_blank">
                <i class="fa fa-support"></i> @@lang('loading_error_help')
            </a>
        </div>
    </div>
    <div class="text-right">
        <button type="button" class="fullpage-loader-close btn btn-link tip" aria-label="@@lang('close')"
                title="@@lang('close')" data-placement="left">
            <span aria-hidden="true"><i class="fa fa-close"></i></span>
        </button>
    </div>
</div>
