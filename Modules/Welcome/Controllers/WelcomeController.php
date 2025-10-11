<?php

namespace Modules\Welcome\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class WelcomeController extends AdminController
{
    /**
     * Render the admin welcome view.
     *
     * @return string The rendered 'welcome' view content.
     */
    public function index()
    {
        return view('welcome');
    }
}