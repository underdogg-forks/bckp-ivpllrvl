<?php

namespace Modules\Core\Routes;

use AllowDynamicProperties;
use MX_Router;

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
// load the MX_Router class
require APPPATH . 'third_party/MX/Router.php';
#[AllowDynamicProperties]
class MyRouter extends MX_Router {}
