<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Welcome\Controllers;

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */
#[AllowDynamicProperties]
class WelcomeController extends \App\Http\Controllers\AdminController
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
