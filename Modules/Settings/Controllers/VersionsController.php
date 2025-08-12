<?php

namespace Modules\Settings\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class VersionsController extends AdminController
{
    /**
     * VersionsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_versions');
    }

    /**
     * @originalName index
     *
     * @originalFile VersionsController.php
     */
    public function index($page = 0)
    {
        $this->mdl_versions->paginate(site_url('versions/index'), $page);
        $versions = $this->mdl_versions->result();
        $this->layout->set('versions', $versions);
        $this->layout->buffer('content', 'settings/versions');
        $this->layout->render();
    }
}
