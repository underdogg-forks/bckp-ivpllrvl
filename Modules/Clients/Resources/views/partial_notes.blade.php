@foreach($client_notes as $client_note) {
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm small">
    <div class="p-6">
        {{ nl2br(e($client_note->client_note)) }}
    </div>
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-muted">
        {{ date_from_mysql($client_note->client_note_date, true) }}
        <span data-id="{{ $client_note->client_note_id }}" class="delete_client_note float-right inline-flex items-center gap-1 px-2 py-1 bg-red-600 dark:bg-red-500 border border-transparent rounded-sm text-xs font-medium text-white hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                <i class="fa fa-trash-o"></i> {{ trans('delete') }}
            </span>
    </div>
</div>
@endforeach
