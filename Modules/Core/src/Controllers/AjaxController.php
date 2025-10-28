<?php

namespace Modules\Core\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    public $ajax_controller = true;

    /**
     * Output the email template for the POSTed `email_template_id` as JSON.
     *
     * Reads `email_template_id` from POST data and echoes the corresponding email
     * template record encoded as JSON.
     *
     * @return void
     */
    public function getContent()
    {
        $id = $this->input->post('email_template_id');
        echo json_encode((new EmailTemplatesService())->getById($id));
    }
}
