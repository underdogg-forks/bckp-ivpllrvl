<?php

namespace Modules\Core\Loaders;

use AllowDynamicProperties;
use MX_Loader;

// load the MX_Loader class
require APPPATH . 'third_party/MX/Loader.php';
#[AllowDynamicProperties]
class MYLoader extends MX_Loader {}
