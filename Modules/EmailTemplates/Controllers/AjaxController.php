<?php

namespace Modules\EmailTemplates\Controllers;

use Illuminate\Http\Request;
use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\EmailTemplates\Services\EmailTemplatesService;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    public $ajax_controller = true;

    /**
     * Output the email template for the POSTed `email_template_id` as JSON.
     *
     * Reads `email_template_id` from POST data and returns the corresponding email
     * template record encoded as JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContent(Request $request): \Illuminate\Http\JsonResponse {
        $id = $request->post('email_template_id');
        return response()->json((new EmailTemplatesService())->getById($id));
    }
}
