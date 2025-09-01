<?php

namespace Modules\Clients\Services;

use AllowDynamicProperties;
use Modules\Clients\Models\ClientNote;
use Modules\Core\Services\BaseService;

#[AllowDynamicProperties]
class ClientNotesService extends BaseService
{
    public $table = 'ip_client_notes';

    public $primary_key = 'ip_client_notes.client_note_id';

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile ClientNote.php
     */
    public function defaultOrderBy(): \Illuminate\Database\Eloquent\Builder
    {
        return ClientNote::query()->orderByDesc('client_note_date');
    }

    /**
     * @originalName validationRules
     *
     * @originalFile ClientNote.php
     */
    public function validationRules()
    {
        return ['client_id' => ['field' => 'client_id', 'label' => trans('client'), 'rules' => 'required'], 'client_note' => ['field' => 'client_note', 'label' => trans('note'), 'rules' => 'required']];
    }

    /**
     * @originalName dbArray
     *
     * @originalFile ClientNote.php
     */
    public function dbArray()
    {
        $db_array                     = parent::dbArray();
        $db_array['client_note_date'] = date('Y-m-d');

        return $db_array;
    }

    /**
     * @originalName delete
     *
     * @originalFile ClientNote.php
     */
    public function delete($id): bool
    {
        parent::delete($id);

        // For AjaxController Check if deletion was successful
        return true;
    }
}
