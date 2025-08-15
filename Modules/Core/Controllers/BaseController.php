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
        $this->config->load('invoice_plane');
        // Don't allow non-ajax requests to ajax Controllers
        if ($this->ajax_controller && ! $this->input->is_ajax_request()) {
            exit;
        }
        $this->load->helper('url');
        // Globally disallow GET requests to delete methods
        if (mb_strstr(current_url(), 'delete') && $this->input->method() !== 'post') {
            show_404();
        }
        // Load basic stuff
        $this->load->library('session');
        $this->load->helper('redirect');
        // Check if database has been configured
        if ( ! env_bool('SETUP_COMPLETED')) {
            redirect()->route('/welcome');
        } else {
            $this->load->library(['encryption', 'form_validation', 'session', 'Modules\Core\Libraries\ClientTitleEnum']);
            $this->load->database();
            $this->load->helper(['trans', 'number', 'pager', 'invoice', 'date', 'form', 'echo', 'user', 'client', 'country']);
            // Load setting model and load settings
            $this->load->model('settings/mdl_settings');
            if ($this->mdl_settings != null) {
                $this->mdl_settings->loadSettings();
            }
            $this->load->helper('settings');
            // Load the lang based on user config, fall back to system if needed
            $user_lang = $this->session->userdata('user_language');
            if (empty($user_lang) || $user_lang == 'system') {
                set_language(get_setting('default_language'));
            } else {
                set_language($user_lang);
            }
            $this->load->helper('lang');
            // Load the layout module to start building the app
            $this->load->module('layout');
        }
    }
}
