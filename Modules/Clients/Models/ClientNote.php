<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Clients\Models;

use Modules\Core\Models\ResponseModel;

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */
#[AllowDynamicProperties]
class ClientNote extends ResponseModel
{
    public $table = 'ip_client_notes';
    public $primary_key = 'ip_client_notes.client_note_id';
    /**
     * @originalName defaultOrderBy
     *
     * @originalFile ClientNote.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('ip_client_notes.client_note_date DESC');
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
        $db_array = parent::dbArray();
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
