<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Core\Controllers;

use AllowDynamicProperties;
/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */
#[AllowDynamicProperties]
class UserController extends BaseController
{
    /**
     * Modules\Core\Controllers\User_Controller constructor.
     *
     * @param string $required_key
     * @param int    $required_val
     */
    public function __construct($required_key, $required_val)
    {
        parent::__construct();
        if ($this->session->userdata($required_key) != $required_val) {
            session_destroy();
            redirect('sessions/login');
        }
    }
}
