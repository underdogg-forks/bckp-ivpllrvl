<?php

namespace Modules\Setup\Controllers;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */
#[AllowDynamicProperties]
class SetupController extends MX_Controller
{
    public $errors = 0;
    /**
     * SetupController constructor.
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
        $this->load->model('settings/mdl_settings');
        // For get_setting() in echo_helper
        $this->load->model('setup/mdl_setup');
        $this->load->module('layout');
        if (!$this->session->userdata('ip_lang')) {
            $this->session->set_userdata('ip_lang', 'en');
        } else {
            set_language($this->session->userdata('ip_lang'));
        }
        $this->lang->load('ip', $this->session->userdata('ip_lang'));
    }
    /**
     * @originalName index
     * @originalFile SetupController.php
     */
    public function index(): void
    {
        redirect('setup/lang');
    }
    /**
     * @originalName lang
     * @originalFile SetupController.php
     */
    public function language(): void
    {
        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('ip_lang', $this->input->post('ip_lang'));
            $this->session->set_userdata('install_step', 'prerequisites');
            redirect('setup/prerequisites');
        }
        // Reset the session cache
        $this->session->unset_userdata('install_step');
        $this->session->unset_userdata('is_upgrade');
        // GetController all languages
        $languages = get_available_languages();
        $this->layout->set('languages', $languages);
        $this->layout->buffer('content', 'setup/lang');
        $this->layout->render('setup');
    }
    /**
     * @originalName prerequisites
     * @originalFile SetupController.php
     */
    public function prerequisites(): void
    {
        if ($this->session->userdata('install_step') != 'prerequisites') {
            redirect('setup/lang');
        }
        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('install_step', 'configure_database');
            redirect('setup/configure_database');
        }
        $this->layout->set(['basics' => $this->checkBasics(), 'writables' => $this->checkWritables(), 'errors' => $this->errors]);
        $this->layout->buffer('content', 'setup/prerequisites');
        $this->layout->render('setup');
    }
    /**
     * @originalName configureDatabase
     * @originalFile SetupController.php
     */
    public function configureDatabase(): void
    {
        if ($this->session->userdata('install_step') != 'configure_database') {
            redirect('setup/prerequisites');
        }
        if ($this->input->post('btn_continue')) {
            $this->loadCiDatabase();
            // This might be an upgrade - check if it is
            if (!$this->db->table_exists('ip_versions')) {
                // This appears to be an install
                $this->session->set_userdata('install_step', 'install_tables');
                redirect('setup/install_tables');
            } else {
                // This appears to be an upgrade
                $this->session->set_userdata('is_upgrade', true);
                $this->session->set_userdata('install_step', 'upgrade_tables');
                redirect('setup/upgrade_tables');
            }
        }
        if ($this->input->post('db_hostname')) {
            // Write a new database configuration to the ipconfig.php file
            $this->writeDatabaseConfig($this->input->post('db_hostname'), $this->input->post('db_username'), $this->input->post('db_password'), $this->input->post('db_database'), $this->input->post('db_port'));
        }
        // Check if the set credentials are correct
        $check_database = $this->checkDatabase();
        $this->layout->set('database', $check_database);
        $this->layout->set('errors', $this->errors);
        $this->layout->buffer('content', 'setup/configure_database');
        $this->layout->render('setup');
    }
    /**
     * @originalName installTables
     * @originalFile SetupController.php
     */
    public function installTables(): void
    {
        if ($this->session->userdata('install_step') != 'install_tables') {
            redirect('setup/prerequisites');
        }
        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('install_step', 'upgrade_tables');
            redirect('setup/upgrade_tables');
        }
        $this->loadCiDatabase();
        $this->layout->set(['success' => $this->mdl_setup->installTables(), 'errors' => $this->mdl_setup->errors]);
        $this->layout->buffer('content', 'setup/install_tables');
        $this->layout->render('setup');
    }
    /**
     * @originalName upgradeTables
     * @originalFile SetupController.php
     */
    public function upgradeTables(): void
    {
        if ($this->session->userdata('install_step') != 'upgrade_tables') {
            redirect('setup/prerequisites');
        }
        if ($this->input->post('btn_continue')) {
            if (!$this->session->userdata('is_upgrade')) {
                $this->session->set_userdata('install_step', 'create_user');
                redirect('setup/create_user');
            } else {
                $this->session->set_userdata('install_step', 'calculation_info');
                redirect('setup/calculation_info');
            }
        }
        $this->loadCiDatabase();
        // Set a new encryption key if none exists
        if (env('ENCRYPTION_KEY') === null || env('ENCRYPTION_KEY') === '') {
            $this->setEncryptionKey();
        }
        $this->layout->set(['success' => $this->mdl_setup->upgradeTables(), 'errors' => $this->mdl_setup->errors]);
        $this->layout->buffer('content', 'setup/upgrade_tables');
        $this->layout->render('setup');
    }
    /**
     * @originalName createUser
     * @originalFile SetupController.php
     */
    public function createUser(): void
    {
        if ($this->session->userdata('install_step') != 'create_user') {
            redirect('setup/prerequisites');
        }
        $this->loadCiDatabase();
        $this->load->model('users/mdl_users');
        $this->load->helper('country');
        if ($this->mdl_users->run_validation()) {
            $db_array = $this->mdl_users->dbArray();
            $db_array['user_type'] = 1;
            $this->mdl_users->save(null, $db_array);
            $this->session->set_userdata('install_step', 'calculation_info');
            redirect('setup/calculation_info');
        }
        $this->layout->set(['countries' => get_country_list(trans('cldr')), 'languages' => get_available_languages()]);
        $this->layout->buffer('content', 'setup/create_user');
        $this->layout->render('setup');
    }
    /**
     * @originalName calculationInfo
     * @originalFile SetupController.php
     */
    public function calculationInfo(): void
    {
        if ($this->session->userdata('install_step') != 'calculation_info') {
            redirect('setup/prerequisites');
        }
        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('install_step', 'complete');
            redirect('setup/complete');
        } elseif ($this->input->post('btn_agree')) {
            $this->writeCalculationConfig();
            $this->session->set_userdata('install_step', 'complete');
            redirect('setup/complete');
        }
        $checkCalculation = $this->checkCalculationConfig();
        if ($checkCalculation['needs_config'] === false) {
            $this->session->set_userdata('install_step', 'complete');
            redirect('setup/complete');
        }
        $this->layout->set('calculation_check', $checkCalculation);
        $this->layout->buffer('content', 'setup/calculation_info');
        $this->layout->render('setup');
    }
    /**
     * @originalName complete
     * @originalFile SetupController.php
     */
    public function complete(): void
    {
        if ($this->session->userdata('install_step') != 'complete') {
            redirect('setup/prerequisites');
        }
        $this->loadCiDatabase();
        $users = $this->db->query('SELECT * FROM ip_users');
        if ($users->num_rows() === 0) {
            log_message('error', 'there was already one or more users in the database');
            $this->session->set_flashdata('alert_error', 'Something went wrong, check the log file for errors');
            $this->session->set_userdata('install_step', 'create_user');
            redirect('setup/create_user');
        }
        // Additional tasks after setup is completed
        $this->postSetupTasks();
        // Check if this is an update or the first install
        // First get all version entries from the database and format them
        $versions = $this->db->query('SELECT * FROM ip_versions');
        if ($versions->num_rows() > 0) {
            foreach ($versions->result() as $row) {
                $data[] = $row;
            }
        }
        // Then check if the first version entry is less than 30 minutes old
        // If yes we assume that the user ran the setup a few minutes ago
        $update = $data[0]->version_date_applied < time() - 1800;
        $this->layout->set('update', $update);
        $this->layout->buffer('content', 'setup/complete');
        $this->layout->render('setup');
        $this->session->sess_destroy();
    }
    /**
     * @originalName checkBasics
     * @originalFile SetupController.php
     */
    private function checkBasics(): array
    {
        $checks = [];
        $php_required = '5.6';
        $php_installed = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
        if ($php_installed < $php_required) {
            $this->errors += 1;
            $checks[] = ['message' => sprintf(trans('php_version_fail'), $php_installed, $php_required), 'success' => 0];
        } else {
            $checks[] = ['message' => trans('php_version_success'), 'success' => 1];
        }
        if (!ini_get('date.timezone')) {
            $checks[] = ['message' => sprintf(trans('php_timezone_fail'), date_default_timezone_get()), 'success' => 1, 'warning' => 1];
        } else {
            $checks[] = ['message' => trans('php_timezone_success'), 'success' => 1];
        }
        return $checks;
    }
    /**
     * @originalName checkWritables
     * @originalFile SetupController.php
     */
    private function checkWritables(): array
    {
        $checks = [];
        $writables = [IPCONFIG_FILE, UPLOADS_FOLDER, UPLOADS_ARCHIVE_FOLDER, UPLOADS_CFILES_FOLDER, UPLOADS_TEMP_FOLDER, UPLOADS_TEMP_MPDF_FOLDER, LOGS_FOLDER];
        foreach ($writables as $writable) {
            $writable_check = ['message' => '<code>' . str_replace(FCPATH, '', $writable) . '</code>&nbsp;', 'success' => 1];
            if (!is_writable($writable)) {
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
     * @originalFile SetupController.php
     */
    private function loadCiDatabase()
    {
        $this->load->database();
    }
    /**
     * @originalName writeDatabaseConfig
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
     * @originalFile SetupController.php
     */
    private function checkDatabase(): array
    {
        // Reload the ipconfig.php file
        global $dotenv;
        $dotenv->load();
        // Load the database config and configure it to test the connection
        include APPPATH . 'config/database.php';
        $db = $db['default'];
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
        if (!$can_connect) {
            $this->errors += 1;
            return ['message' => trans('setup_db_cannot_connect'), 'success' => false];
        }
        return ['message' => trans('database_properly_configured'), 'success' => true];
    }
    /**
     * @originalName setEncryptionKey
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
     * @originalName postSetupTasks
     * @originalFile SetupController.php
     */
    private function postSetupTasks()
    {
        // Set SETUP_COMPLETED to true
        $config = file_get_contents(IPCONFIG_FILE);
        $config = preg_replace('/SETUP_COMPLETED=(.*)?/', 'SETUP_COMPLETED=true', $config);
        write_file(IPCONFIG_FILE, $config);
    }
    /**
     * @originalName checkCalculationConfig
     * @originalFile SetupController.php
     */
    private function checkCalculationConfig(): array
    {
        $this->loadCiDatabase();
        $this->load->model('settings/mdl_versions');
        $current_version = $this->mdl_versions->getCurrentVersion();
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
     * @originalName writeCalculationConfig
     * @originalFile SetupController.php
     */
    private function writeCalculationConfig()
    {
        $config = file_get_contents(IPCONFIG_FILE);
        $config .= PHP_EOL . 'LEGACY_CALCULATION=false';
        write_file(IPCONFIG_FILE, $config);
    }
}
