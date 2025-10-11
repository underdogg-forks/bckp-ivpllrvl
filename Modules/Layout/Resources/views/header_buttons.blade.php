
<div class="headerbar-item float-right">
    <div class="inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm">
        @if(!isset($hide_submit_button))
        <button id="btn-submit" name="btn_submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors ajax-loader" value="1">
            <i class="fa fa-check"></i> @lang('save')
        </button>
        @php
            }
            @if(!isset($hide_cancel_button)) {
                $attribute_cancel = empty($attribute_cancel) ? 'onclick="window.history.back()"' : $attribute_cancel;

        <button type="button" {{ $attribute_cancel }} id="btn-cancel" name="btn_cancel"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors ajax-loader" value="1">
            <i class="fa fa-times"></i> @lang('cancel')
        </button>
        @endif
    </div>
</div>
