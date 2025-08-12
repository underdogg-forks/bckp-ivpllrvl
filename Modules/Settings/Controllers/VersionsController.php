<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Settings\Controllers;

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */
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
