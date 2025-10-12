<?php

namespace Modules\Settings\Controllers;

use Illuminate\Contracts\View\View;

use AllowDynamicProperties;
use Illuminate\Http\Request;
use Modules\Core\Controllers\AdminController;
use Modules\Settings\Services\VersionsService;

#[AllowDynamicProperties]
class VersionsController extends AdminController
{
    public function index(Request $request, int $page = 0): \Illuminate\Contracts\View\View
    {
        $service = new VersionsService();
        $service->paginate(route('versions.index'), $page);
        $versions = $service->result();

        return view('settings.index', [
            'versions' => $versions,
        ]);
    }
}
