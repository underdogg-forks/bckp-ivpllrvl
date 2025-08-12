<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Core\Routes;

use AllowDynamicProperties;
use MX_Router;
// load the MX_Router class
require APPPATH . 'third_party/MX/Router.php';
#[AllowDynamicProperties]
class MyRouter extends MX_Router
{
}
