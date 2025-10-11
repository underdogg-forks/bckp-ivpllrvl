@foreach($client_notes as $client_note) {
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm small">
    <div class="p-6">
        {{ nl2br(e($client_note->client_note)) }}
    </div>
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-muted">
        {{ date_from_mysql($client_note->client_note_date, true) }}
        <span data-id="{{ $client_note->client_note_id }}" class="delete_client_note float-right btn px-2 py-1 text-xs -danger">
                <i class="fa fa-trash-o"></i> {{ trans('delete') }}
            </span>
    </div>
</div>
@endforeach
