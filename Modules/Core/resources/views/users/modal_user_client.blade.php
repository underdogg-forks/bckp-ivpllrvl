<script>
    $(function () {
        var user_client_modal = $('#add-user-client');
        user_client_modal.modal('show');

        // Select2 for all select inputs
        $(".simple-select").select2();

        $('#btn_user_client').click(function () {
            $.post("{{ url('users/ajax/save_user_client') }}", {
                user_id: '{{ $user_id }}',
                client_id: $('#client_id').val()
            }, function (data) {
                @if(config('app.ip_debug'))
                    'console.log(data);'
                @else
                    ''
                @endif
                $('#div_user_client_table').load('{{ url('users/ajax/load_user_client_table') }}', {
                    user_id: '{{ $user_id }}'
                });

                user_client_modal.modal('hide');
                $('#modal-placeholder').text('');
            });
        });
    });
</script>

<div id="add-user-client" class="modal modal-lg" role="dialog" aria-labelledby="modal_add_user_client"
     aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">@lang('add_client')</h4>
        </div>
        <div class="modal-body">

            <div class="mb-4">
                <label for="client_id">@lang('client')</label>
                <select name="client_id" id="client_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors simple-select" autofocus="autofocus">
                    @foreach($clients as $client)
{
                    <option value="  $client->client_id  ">  htmlsc(format_client($client))  </option>}@endforeach
                </select>
            </div>

        </div>

        <div class="modal-footer">
            <div class="inline-flex rounded-md shadow-sm">
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors" id="btn_user_client" type="button">
                    <i class="fa fa-check"></i> @lang('submit')
                </button>
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> @lang('cancel')
                </button>
            </div>
        </div>

    </form>

</div>
