<?php

namespace Modules\Core\Loaders;

use AllowDynamicProperties;
use MX_Loader;

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
// load the MX_Loader class
require APPPATH . 'third_party/MX/Loader.php';
#[AllowDynamicProperties]
class MYLoader extends MX_Loader {}
