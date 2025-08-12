<?php

namespace Modules\Settings\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

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
