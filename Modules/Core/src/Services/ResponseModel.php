<?php

namespace Modules\Core\Services;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class ResponseModel
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
