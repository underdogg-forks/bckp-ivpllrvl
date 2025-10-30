<?php

namespace Modules\Core\Controllers;

use AllowDynamicProperties;
use App\Http\Controllers\Controller as MXController;

#[AllowDynamicProperties]
class BaseController extends MXController
{
    /** @var bool */
    public $ajax_controller = false;

    /**
     * Modules\Core\Controllers\Base_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        // Don't allow non-ajax requests to ajax Controllers
        if ($this->ajax_controller && ! request()->ajax()) {
            abort(403);
        }
        
        // Globally disallow GET requests to delete methods
        if (str_contains(request()->url(), 'delete') && request()->method() !== 'POST') {
            abort(404);
        }
        
        // Check if database has been configured
        if ( ! config('app.setup_completed', false)) {
            redirect()->route('welcome');
        } else {
            // Load settings
            (new SettingsService())->loadSettings();
            
            // Load the lang based on user config, fall back to system if needed
            $user_lang = session('user_language');
            if (empty($user_lang) || $user_lang == 'system') {
                set_language(get_setting('default_language'));
            } else {
                set_language($user_lang);
            }
        }
    }
}
