<?php

namespace Modules\Settings\Controllers;

if ( ! defined('BASEPATH')) {
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
     * @originalName getCronKey
     *
     * @originalFile AjaxController.php
     */
    public function getCronKey()
    {
        $this->load->helper('string');
        echo random_string('alnum', 16);
    }
}
