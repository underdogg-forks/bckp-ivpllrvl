<?php

namespace Modules\EmailTemplates\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    public $ajax_controller = true;

    /**
     * @originalName getContent
     *
     * @originalFile AjaxController.php
     */
    public function getContent()
    {
        $id = $this->input->post('email_template_id');
        echo json_encode((new EmailTemplatesService())->getById($id));
    }
}
