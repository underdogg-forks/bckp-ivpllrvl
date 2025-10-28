<?php

namespace Modules\Core\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class WelcomeController extends AdminController
{
    /**
     * Renders the admin welcome view.
     *
     * @return string the rendered 'welcome' view content
     */
    public function index()
    {
        return view('welcome');
    }
}
