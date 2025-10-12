<?php

namespace Modules\Core\Controllers;

use AllowDynamicProperties;
use App\Http\Controllers\Controller as MXController;
use Modules\Settings\Services\SettingsService;

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
            exit;
        }
        // Globally disallow GET requests to delete methods
        if (mb_strstr(current_url(), 'delete') && request()->method() !== 'POST') {
            show_404();
        }
        // Check if database has been configured
        if ( ! env_bool('SETUP_COMPLETED')) {
            return redirect()->route('/welcome');
        } else {
            // Load settings
            $mdl_settings = app('Modules\Settings\Models\MdlSettings');
            if ($mdl_settings != null) {
                (new SettingsService())->loadSettings();
            }
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
