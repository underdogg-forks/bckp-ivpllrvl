<?php

namespace Modules\Core\Controllers;

use AllowDynamicProperties;

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
        if (session($required_key) != $required_val) {
            session_destroy();
            redirect()->route('sessions/login');
        }
    }
}
