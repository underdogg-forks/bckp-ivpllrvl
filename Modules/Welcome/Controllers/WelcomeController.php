<?php

namespace Modules\Welcome\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class WelcomeController extends AdminController
{
    /**
     * @originalName index
     *
     * @originalFile WelcomeController.php
     */
    public function index()
    {
        $this->load->model('settings/mdl_settings');
        $this->load->helper(['settings', 'echo', 'url']);
        $this->load->view('welcome');
    }
}
