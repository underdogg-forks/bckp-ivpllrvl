<?php

namespace Modules\Setup\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AllowDynamicProperties;
use App\Http\Controllers\Controller as MXController;
use Illuminate\Support\Facades\Log;
use Modules\Setup\Services\SetupService;
use Modules\Users\Services\UsersService;

#[AllowDynamicProperties]
class SetupController extends MXController
{
    public $errors = 0;

    /**
     * Enforces setup availability, loads required framework resources, and initializes localization.
     *
     * Aborts with HTTP 403 if the DISABLE_SETUP environment flag is true. Loads the session library,
     * necessary helpers and the layout module, ensures the session key `ip_lang` exists (defaults to
     * `'en'`) and applies that language, then loads the `ip` language file.
     */
    public function __construct()
    {
        if (env_bool('DISABLE_SETUP', false)) {
            show_error('The setup is disabled.', 403);
        }
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('file');
        $this->load->helper('directory');
        $this->load->helper('url');
        $this->load->helper('lang');
        $this->load->helper('trans');
        $this->load->helper('settings');
        $this->load->helper('echo');
        // For get_setting() in echo_helper
        $this->load->module('layout');
        if ( ! session('ip_lang')) {
            session()->put('ip_lang', 'en');
        } else {
            set_language(session('ip_lang'));
        }
        $this->lang->load('ip', session('ip_lang'));
    }

    /**
     * @originalName index
     *
     * @originalFile SetupController.php
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('setup/lang');
    }

    /**
     * @originalName lang
     *
     * @originalFile SetupController.php
     */
    public function language(Request $request): RedirectResponse|Response {
        if ($request->post('btn_continue')) {
            session()->put('ip_lang', $request->post('ip_lang'));
            session()->put('install_step', 'prerequisites');
            return redirect()->route('setup/prerequisites');
        }
        // Reset the session cache
        $this->session->unset_userdata('install_step');
        $this->session->unset_userdata('is_upgrade');
        // GetController all languages
        $languages = get_available_languages();
        $this->layout->set('languages', $languages);
        $this->layout->buffer('content', 'setup/lang');
        return $this->renderLayout('setup');
    }

    /**
     * @originalName prerequisites
     *
     * @originalFile SetupController.php
     */
    public function prerequisites(Request $request): RedirectResponse|Response {
        if (session('install_step') != 'prerequisites') {
            return redirect()->route('setup/lang');
        }
        if ($request->post('btn_continue')) {
            session()->put('install_step', 'configure_database');
            return redirect()->route('setup/configure_database');
        }
        $this->layout->set(['basics' => $this->checkBasics(), 'writables' => $this->checkWritables(), 'errors' => $this->errors]);
        $this->layout->buffer('content', 'setup/prerequisites');
        return $this->renderLayout('setup');
    }

    /**
     * @originalName configureDatabase
     *
     * @originalFile SetupController.php
     */
    public function configureDatabase(Request $request): RedirectResponse|Response {
        if (session('install_step') != 'configure_database') {
            return redirect()->route('setup/prerequisites');
        }
        if ($request->post('btn_continue')) {
            $this->loadCiDatabase();
            // This might be an upgrade - check if it is
            if ( ! $this->db->table_exists('ip_versions')) {
                // This appears to be an install
                session()->put('install_step', 'install_tables');
                return redirect()->route('setup/install_tables');
            } else {
                // This appears to be an upgrade
                session()->put('is_upgrade', true);
                session()->put('install_step', 'upgrade_tables');
                return redirect()->route('setup/upgrade_tables');
            }
        }
        if ($request->post('db_hostname')) {
            // Write a new database configuration to the ipconfig.php file
            $this->writeDatabaseConfig($request->post('db_hostname'), $request->post('db_username'), $request->post('db_password'), $request->post('db_database'), $request->post('db_port'));
        }
        // Check if the set credentials are correct
        $check_database = $this->checkDatabase();
        $this->layout->set('database', $check_database);
        $this->layout->set('errors', $this->errors);
        $this->layout->buffer('content', 'setup/configure_database');
        return $this->renderLayout('setup');
    }

    /**
     * @originalName installTables
     *
     * @originalFile SetupController.php
     */
    public function installTables(Request $request): RedirectResponse|Response {
        if (session('install_step') != 'install_tables') {
            return redirect()->route('setup/prerequisites');
        }
        if ($request->post('btn_continue')) {
            session()->put('install_step', 'upgrade_tables');
            return redirect()->route('setup/upgrade_tables');
        }
        $this->loadCiDatabase();
        $this->layout->set(['success' => (new SetupService())->installTables(), 'errors' => (new SetupService())->errors]);
        $this->layout->buffer('content', 'setup/install_tables');
        return $this->renderLayout('setup');
    }

    /**
     * Handle the database upgrade step of the setup flow.
     *
     * Validates the current install step and redirects to prerequisites if not allowed.
     * On form submission advances the install flow to either the create-user or calculation-info step and redirects.
     * Ensures the database is loaded and an encryption key exists, runs table upgrade operations via the setup service,
     * and renders the upgrade view with the operation results and any errors.
     */
    public function upgradeTables(Request $request): RedirectResponse|Response {
        if (session('install_step') != 'upgrade_tables') {
            return redirect()->route('setup/prerequisites');
        }
        if ($request->post('btn_continue')) {
            if ( ! session('is_upgrade')) {
                session()->put('install_step', 'create_user');
                return redirect()->route('setup/create_user');
            } else {
                session()->put('install_step', 'calculation_info');
                return redirect()->route('setup/calculation_info');
            }
        }
        $this->loadCiDatabase();
        // Set a new encryption key if none exists
        if (env('ENCRYPTION_KEY') === null || env('ENCRYPTION_KEY') === '') {
            $this->setEncryptionKey();
        }
        $this->layout->set(['success' => (new SetupService())->upgradeTables(), 'errors' => (new SetupService())->errors]);
        $this->layout->buffer('content', 'setup/upgrade_tables');
        return $this->renderLayout('setup');
    }

    /**
     * Handle the "create user" setup step and create the initial admin user when valid.
     *
     * Validates submitted user data; if validation succeeds, creates a user with `user_type` = 1,
     * advances the `install_step` session value to `calculation_info`, and redirects to the calculation info step.
     * If not submitted or validation fails, prepares country and language data for the layout and renders the user creation form.
     */
    public function createUser(Request $request): RedirectResponse|Response
    {
        if (session('install_step') != 'create_user') {
            return redirect()->route('setup/prerequisites');
        }
        $this->loadCiDatabase();
        $this->load->helper('country');
        if ((new UsersService())->runValidation(null, $request)) {
            $db_array              = (new UsersService())->dbArray($request);
            $db_array['user_type'] = 1;
            (new UsersService())->save($request, null, $db_array);
            session()->put('install_step', 'calculation_info');
            return redirect()->route('setup/calculation_info');
        }
        $this->layout->set(['countries' => get_country_list(trans('cldr')), 'languages' => get_available_languages()]);
        $this->layout->buffer('content', 'setup/create_user');
        return $this->renderLayout('setup');
    }

    /**
     * @originalName calculationInfo
     *
     * @originalFile SetupController.php
     */
    public function calculationInfo(Request $request): RedirectResponse|Response {
        if (session('install_step') != 'calculation_info') {
            return redirect()->route('setup/prerequisites');
        }
        if ($request->post('btn_continue')) {
            session()->put('install_step', 'complete');
            return redirect()->route('setup/complete');
        } elseif ($request->post('btn_agree')) {
            $this->writeCalculationConfig();
            session()->put('install_step', 'complete');
            return redirect()->route('setup/complete');
        }
        $checkCalculation = $this->checkCalculationConfig();
        if ($checkCalculation['needs_config'] === false) {
            session()->put('install_step', 'complete');
            return redirect()->route('setup/complete');
        }
        $this->layout->set('calculation_check', $checkCalculation);
        $this->layout->buffer('content', 'setup/calculation_info');
        return $this->renderLayout('setup');
    }

    /**
     * @originalName complete
     *
     * @originalFile SetupController.php
     */
    public function complete(): RedirectResponse|Response
    {
        if (session('install_step') != 'complete') {
            return redirect()->route('setup/prerequisites');
        }
        $this->loadCiDatabase();
        $users = $this->db->query('SELECT * FROM ip_users');
        if ($users->numRows() === 0) {
            Log::error('there was already one or more users in the database');
            $this->session->set_flashdata('alert_error', 'Something went wrong, check the log file for errors');
            session()->put('install_step', 'create_user');
            return redirect()->route('setup/create_user');
        }
        // Additional tasks after setup is completed
        $this->postSetupTasks();
        // Check if this is an update or the first install
        // First get all version entries from the database and format them
        $versions = $this->db->query('SELECT * FROM ip_versions');
        if ($versions->numRows() > 0) {
            foreach ($versions->result() as $row) {
                $data[] = $row;
            }
        }
        // Then check if the first version entry is less than 30 minutes old
        // If yes we assume that the user ran the setup a few minutes ago
        $update = $data[0]->version_date_applied < time() - 1800;
        $this->layout->set('update', $update);
        $this->layout->buffer('content', 'setup/complete');
        $response = $this->renderLayout('setup');
        $this->session->sess_destroy();
        return $response;
    }

    private function renderLayout(string $view): Response
    {
        ob_start();
        $this->layout->render($view);
        $content = ob_get_clean();

        return response($content);
    }

    /**
     * @originalName checkBasics
     *
     * @originalFile SetupController.php
     */
    private function checkBasics(): array
    {
        $checks        = [];
        $php_required  = '5.6';
        $php_installed = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
        if ($php_installed < $php_required) {
            $this->errors += 1;
            $checks[] = ['message' => sprintf(trans('php_version_fail'), $php_installed, $php_required), 'success' => 0];
        } else {
            $checks[] = ['message' => trans('php_version_success'), 'success' => 1];
        }
        if ( ! ini_get('date.timezone')) {
            $checks[] = ['message' => sprintf(trans('php_timezone_fail'), date_default_timezone_get()), 'success' => 1, 'warning' => 1];
        } else {
            $checks[] = ['message' => trans('php_timezone_success'), 'success' => 1];
        }

        return $checks;
    }

    /**
     * @originalName checkWritables
     *
     * @originalFile SetupController.php
     */
    private function checkWritables(): array
    {
        $checks    = [];
        $writables = [IPCONFIG_FILE, UPLOADS_FOLDER, UPLOADS_ARCHIVE_FOLDER, UPLOADS_CFILES_FOLDER, UPLOADS_TEMP_FOLDER, UPLOADS_TEMP_MPDF_FOLDER, LOGS_FOLDER];
        foreach ($writables as $writable) {
            $writable_check = ['message' => '<code>' . str_replace(FCPATH, '', $writable) . '</code>&nbsp;', 'success' => 1];
            if ( ! is_writable($writable)) {
                $writable_check['message'] .= trans('is_not_writable');
                $writable_check['success'] .= 0;
                $this->errors += 1;
            } else {
                $writable_check['message'] .= trans('is_writable');
            }
            $checks[] = $writable_check;
        }

        return $checks;
    }

    /**
     * @originalName loadCiDatabase
     *
     * @originalFile SetupController.php
     */
    private function loadCiDatabase()
    {
        $this->load->database();
    }

    /**
     * @originalName writeDatabaseConfig
     *
     * @originalFile SetupController.php
     */
    private function writeDatabaseConfig(string $hostname, string $username, string $password, string $database, $port = 3306)
    {
        $config = file_get_contents(IPCONFIG_FILE);
        $config = preg_replace('/DB_HOSTNAME=(.*)?/', "DB_HOSTNAME='" . $hostname . "'", $config);
        $config = preg_replace('/DB_USERNAME=(.*)?/', "DB_USERNAME='" . $username . "'", $config);
        $config = preg_replace('/DB_PASSWORD=(.*)?/', "DB_PASSWORD='" . $password . "'", $config);
        $config = preg_replace('/DB_DATABASE=(.*)?/', "DB_DATABASE='" . $database . "'", $config);
        $config = preg_replace('/DB_PORT=(.*)?/', 'DB_PORT=' . $port, $config);
        write_file(IPCONFIG_FILE, $config);
    }

    /**
     * @originalName checkDatabase
     *
     * @originalFile SetupController.php
     */
    private function checkDatabase(): array
    {
        // Reload the ipconfig.php file
        global $dotenv;
        $dotenv->load();
        // Load the database config and configure it to test the connection
        include APPPATH . 'config/database.php';
        $db             = $db['default'];
        $db['autoinit'] = false;
        $db['db_debug'] = false;
        // Check if there is some configuration set
        if (empty($db['hostname'])) {
            $this->errors += 1;

            return ['message' => trans('setup_database_message'), 'success' => false];
        }
        // Initialize the database connection, turn off automatic error reporting to display connection issues manually
        error_reporting(0);
        $db_object = $this->load->database($db, true);
        // Try to initialize the database connection
        $can_connect = (bool) $db_object->conn_id;
        if ( ! $can_connect) {
            $this->errors += 1;

            return ['message' => trans('setup_db_cannot_connect'), 'success' => false];
        }

        return ['message' => trans('database_properly_configured'), 'success' => true];
    }

    /**
     * @originalName setEncryptionKey
     *
     * @originalFile SetupController.php
     */
    private function setEncryptionKey()
    {
        $length = env('ENCRYPTION_CIPHER') == 'AES-256' ? 32 : 16;
        if (function_exists('random_bytes')) {
            $key = 'base64:' . base64_encode(random_bytes($length));
        } else {
            $key = 'base64:' . base64_encode(openssl_random_pseudo_bytes($length));
        }
        $config = file_get_contents(IPCONFIG_FILE);
        $config = preg_replace('/ENCRYPTION_KEY=(.*)?/', 'ENCRYPTION_KEY=' . $key, $config);
        write_file(IPCONFIG_FILE, $config);
    }

    /**
     * Mark the application's setup as completed in the IPCONFIG_FILE.
     *
     * Updates the SETUP_COMPLETED entry in the configuration file to `true`.
     */
    private function postSetupTasks()
    {
        // Set SETUP_COMPLETED to true
        $config = file_get_contents(IPCONFIG_FILE);
        $config = preg_replace('/SETUP_COMPLETED=(.*)?/', 'SETUP_COMPLETED=true', $config);
        write_file(IPCONFIG_FILE, $config);
    }

    /**
     * Determines whether the legacy calculation setting requires explicit configuration for the installed version.
     *
     * @return array An associative array describing configuration needs:
     *               - `needs_config` (bool): `true` if manual configuration is required, `false` otherwise.
     *               - `current_value` (string): the current `LEGACY_CALCULATION` value (`'not_set'`, `'true'`, or `'false'`).
     *               - `recommended` (string|null): the recommended value when configuration is required (`'false'`), or `null` when not applicable.
     */
    private function checkCalculationConfig(): array
    {
        $this->loadCiDatabase();
        $current_version = (new VersionsService())->getCurrentVersion();
        if (version_compare($current_version, '1.6.3', '>=')) {
            // Reload the ipconfig.php
            global $dotenv;
            $dotenv->load();
            $legacy_calc = env('LEGACY_CALCULATION');
            if ($legacy_calc === null) {
                return ['needs_config' => true, 'current_value' => 'not_set', 'recommended' => 'false'];
            }
            if ($legacy_calc === 'true' || $legacy_calc === true) {
                return ['needs_config' => true, 'current_value' => 'true', 'recommended' => 'false'];
            }

            return ['needs_config' => false, 'current_value' => 'false'];
        }

        return ['needs_config' => false];
    }

    /**
     * Append the LEGACY_CALCULATION setting to the IPCONFIG file.
     *
     * Reads the contents of IPCONFIG_FILE, appends a newline and the line
     * `LEGACY_CALCULATION=false`, and writes the updated content back to the file.
     */
    private function writeCalculationConfig()
    {
        $config = file_get_contents(IPCONFIG_FILE);
        $config .= PHP_EOL . 'LEGACY_CALCULATION=false';
        write_file(IPCONFIG_FILE, $config);
    }
}
