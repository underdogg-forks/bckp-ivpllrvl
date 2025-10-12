<?php

namespace Modules\Welcome\Controllers;

use AllowDynamicProperties;
use Illuminate\Contracts\View\View;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class WelcomeController extends AdminController
{
    /**
     * Renders the admin welcome view.
     *
     * @return View the rendered 'welcome' view content
     */
    public function index(): View
    {
        return view('welcome');
    }
}
