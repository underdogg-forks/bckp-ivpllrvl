<?php

namespace Modules\Core\Controllers;

use AllowDynamicProperties;

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
        $id = request()->input('email_template_id');
        echo json_encode((new EmailTemplatesService())->getById($id));
    }
}
