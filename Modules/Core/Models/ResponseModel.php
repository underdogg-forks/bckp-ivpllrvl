<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Core\Models;

use AllowDynamicProperties;
/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */
#[AllowDynamicProperties]
class ResponseModel extends FormValidationModel
{
    /**
     * @originalName save
     *
     * @originalFile ResponseModel.php
     */
    public function save($id = null, $db_array = null)
    {
        if ($id) {
            $this->session->set_flashdata('alert_success', trans('record_successfully_updated'));
            parent::save($id, $db_array);
        } else {
            $this->session->set_flashdata('alert_success', trans('record_successfully_created'));
            $id = parent::save(null, $db_array);
        }
        return $id;
    }
    /**
     * @originalName delete
     *
     * @originalFile ResponseModel.php
     */
    public function delete($id)
    {
        parent::delete($id);
        $this->session->set_flashdata('alert_success', trans('record_successfully_deleted'));
    }
}
