<?php

namespace Modules\Core\Controllers;

use AllowDynamicProperties;

use function Modules\Settings\Controllers\random_string;

#[AllowDynamicProperties]
class SettingsAjaxController extends AdminController
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
