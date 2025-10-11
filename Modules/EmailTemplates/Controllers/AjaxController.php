<?php

namespace Modules\EmailTemplates\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    public $ajax_controller = true;

    /**
     * Outputs the email template data for the POSTed `email_template_id` as JSON.
     *
     * Reads `email_template_id` from POST input and echoes the corresponding email
     * template record encoded as JSON.
     */
    public function getContent()
    {
        $id = $this->input->post('email_template_id');
        echo json_encode((new EmailTemplatesService())->getById($id));
    }
}