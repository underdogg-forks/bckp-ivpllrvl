<?php

namespace App\Models;

use AllowDynamicProperties;
use Illuminate\Http\Request;

#[AllowDynamicProperties]
class ResponseModel extends BaseModel
{
    /**
     * @originalName save
     *
     * @originalFile ResponseModel.php
     */
    public function save(\Illuminate\Http\Request $request = null, $id = null, $db_array = null)
    {
        if ($id) {
            $this->session->set_flashdata('alert_success', trans('record_successfully_updated'));
            parent::save($request, $id, $db_array);
        } else {
            $this->session->set_flashdata('alert_success', trans('record_successfully_created'));
            $id = parent::save($request, null, $db_array);
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
