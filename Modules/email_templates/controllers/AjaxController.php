<?php

namespace Modules\Emailtemplates\Controllers;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */
#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    public $ajax_controller = true;
    /**
     * @originalName getContent
     * @originalFile AjaxController.php
     */
    public function getContent()
    {
        $this->load->model('email_templates/mdl_email_templates');
        $id = $this->input->post('email_template_id');
        echo json_encode($this->mdl_email_templates->getById($id));
    }
}
