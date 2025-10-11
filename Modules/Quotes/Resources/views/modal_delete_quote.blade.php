
<div id="delete-quote" class="modal modal-lg" role="dialog" aria-labelledby="modal_delete_quote" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">@lang('delete_quote')</h4>
        </div>
        <div class="modal-body">

            <div class="p-4 mb-4 text-red-700 dark:text-red-200 bg-red-100 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg">@lang('delete_quote_warning')</div>

        </div>
        <div class="modal-footer">
            <form action="{{ url('quotes/delete/' . $quote->quote_id) }}"
                  method="POST">
                @csrf

                <div class="inline-flex rounded-md shadow-sm">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors ajax-loader">
                        <i class="fa fa-trash-o fa-margin"></i> @lang('confirm_deletion')
                    </button>
                    <a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" data-dismiss="modal">
                        <i class="fa fa-times"></i> @lang('cancel')
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
