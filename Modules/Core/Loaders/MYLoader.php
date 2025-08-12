<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Core\Loaders;

use AllowDynamicProperties;
use MX_Loader;
// load the MX_Loader class
require APPPATH . 'third_party/MX/Loader.php';
#[AllowDynamicProperties]
class MYLoader extends MX_Loader
{
}
