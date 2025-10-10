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
        return view('welcome');
    }
}
