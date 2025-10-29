<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\DB;

use AllowDynamicProperties;
use Modules\Core\Models\Version;
use Modules\Invoices\app\Models\InvoiceGroup;
use Modules\Payments\app\Models\PaymentMethod;

use const Modules\Setup\Services\APPPATH;

use function Modules\Setup\Services\directory_map;

use const Modules\Setup\Services\IP_DEBUG;

use function Modules\Setup\Services\random_string;

#[AllowDynamicProperties]
class SetupService extends BaseService
{
    public $errors = [];

    /**
     * @originalName installTables
     *
     * @originalFile Setup.php
     */
    public function installTables()
    {
        $file_contents = file_get_contents(APPPATH . 'modules/setup/sql/000_1.0.0.sql');
        $this->executeContents($file_contents);
        $this->saveVersion('000_1.0.0.sql');
        if ($this->errors) {
            return false;
        }
        $this->installDefaultData();
        $this->installDefaultSettings();

        return true;
    }

    /**
     * Install default data using Eloquent.
     *
     * @return void
     */
    public function installDefaultData(): void
    {
        InvoiceGroup::query()->create(['invoice_group_name' => 'Invoice Default', 'invoice_group_next_id' => 1]);
        InvoiceGroup::query()->create(['invoice_group_name' => 'Quote Default', 'invoice_group_prefix' => 'QUO', 'invoice_group_next_id' => 1]);
        PaymentMethod::query()->create(['payment_method_name' => 'Cash']);
        PaymentMethod::query()->create(['payment_method_name' => 'Credit Card']);
    }

    /**
     * @originalName upgradeTables
     *
     * @originalFile Setup.php
     */
    public function upgradeTables(): bool
    {
        $sql_files = directory_map(APPPATH . 'modules/setup/sql', true);
        sort($sql_files);
        unset($sql_files[0]);
        foreach ($sql_files as $sql_file) {
            if (mb_substr($sql_file, -4) !== '.sql') {
                continue;
            }
            $update_applied = Version::query()->where('version_file', $sql_file)->exists();
            if ($update_applied) {
                continue;
            }
            $file_contents = file_get_contents(APPPATH . 'modules/setup/sql/' . $sql_file);
            $this->executeContents($file_contents);
            $this->saveVersion($sql_file);
            $upgrade_method = 'upgrade_' . str_replace('.', '_', mb_substr($sql_file, 0, -4));
            if ( ! method_exists($this, $upgrade_method)) {
                continue;
            }
            $this->{$upgrade_method}();
        }
        if ($this->errors) {
            return false;
        }
        $this->installDefaultSettings();

        return true;
    }

    /**
     * @originalName upgrade006120
     *
     * @originalFile Setup.php
     */
    public function upgrade006120()
    {
        /* Update alert to notify about the changes with invoice deletion and credit invoices
         * but only display the warning when the previous version is 1.1.2 or lower and it's an update
         * therefore check if it's an update, if the time difference between v1.1.2 and v1.2.0 is
         * greater than 100 and if v1.2.0 was not installed within this update process
         */
        DB::where_in('version_file', ['006_1.2.0.sql', '005_1.1.2.sql']);
        $versions     = DB::get('ip_versions')->result();
        $upgrade_diff = $versions[1]->version_date_applied - $versions[0]->version_date_applied;
        if (session('is_upgrade') && $upgrade_diff > 100 && $versions[1]->version_date_applied > time() - 100) {
            $setup_notice = ['type' => 'alert-danger', 'content' => trans('setup_v120_alert')];
            session(['setup_notice', $setup_notice);
        }
    }

    /**
     * @originalName upgrade019147
     *
     * @originalFile Setup.php
     */
    public function upgrade019147()
    {
        /* Update alert to set the session configuration $config['sess_use_database'] = false to true
         * but only display the warning when the previous version is 1.4.6 or lower and it's an update
         * (see above for details)
         */
        DB::where_in('version_file', ['018_1.4.6.sql', '019_1.4.7.sql']);
        $versions     = DB::get('ip_versions')->result();
        $upgrade_diff = $versions[1]->version_date_applied - $versions[0]->version_date_applied;
        if (session('is_upgrade') && $upgrade_diff > 100 && $versions[1]->version_date_applied > time() - 100) {
            $setup_notice = ['type' => 'alert-danger', 'content' => trans('setup_v147_alert')];
            session(['setup_notice', $setup_notice);
        }
    }

    /**
     * @originalName upgrade023150
     *
     * @originalFile Setup.php
     */
    public function upgrade023150()
    {
        $res          = DB::query('SELECT * FROM ip_custom_fields');
        $drop_columns = [];
        $tables       = ['client', 'invoice', 'quote', 'payment', 'user'];
        if ($res->numRows()) {
            foreach ($res->result() as $row) {
                $drop_columns[] = ['field_id' => $row->custom_field_id, 'column' => $row->custom_field_column, 'table' => $row->custom_field_table];
            }
        }
        // Create tables
        DB::query('CREATE TABLE `ip_client_custom_new`
            (
                `client_custom_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
                `client_id` INT NOT NULL, `client_custom_fieldid` INT NOT NULL,
                `client_custom_fieldvalue` TEXT NULL ,
                UNIQUE (client_id, client_custom_fieldid)
            );');
        DB::query('CREATE TABLE `ip_invoice_custom_new`
            (
            `invoice_custom_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
            `invoice_id` INT NOT NULL, `invoice_custom_fieldid` INT NOT NULL,
            `invoice_custom_fieldvalue` TEXT NULL ,
            UNIQUE (invoice_id, invoice_custom_fieldid)
            );');
        DB::query('CREATE TABLE `ip_quote_custom_new`
            (
                `quote_custom_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
                `quote_id` INT NOT NULL, `quote_custom_fieldid` INT NOT NULL,
                `quote_custom_fieldvalue` TEXT NULL ,
                UNIQUE (quote_id, quote_custom_fieldid)
            );');
        DB::query('CREATE TABLE `ip_payment_custom_new`
            (
                `payment_custom_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
                `payment_id` INT NOT NULL, `payment_custom_fieldid` INT NOT NULL,
                `payment_custom_fieldvalue` TEXT NULL ,
                UNIQUE (payment_id, payment_custom_fieldid)
            );');
        DB::query('CREATE TABLE `ip_user_custom_new`
            (
                `user_custom_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
                `user_id` INT NOT NULL, `user_custom_fieldid` INT NOT NULL,
                `user_custom_fieldvalue` TEXT NULL ,
                UNIQUE (user_id, user_custom_fieldid)
            );');
        // Migrate Data
        foreach ($drop_columns as $value) {
            $res = DB::query('SELECT * FROM ' . $value['table']);
            preg_match('/^ip_(.*?)_custom$/i', $value['table'], $matches);
            $table_type = $matches[1];
            $table_name = $value['table'] . '_new';
            if ($res->numRows()) {
                foreach ($res->result() as $row) {
                    $escaped_table_type = DB::escape($row->{$table_type . '_id'});
                    $escaped_column     = DB::escape($row->{$value['column']});
                    $query              = "INSERT INTO {$table_name}\n                        (" . $table_type . '_id, ' . $table_type . '_custom_fieldid, ' . $table_type . "_custom_fieldvalue)\n                        VALUES (\n                            {$escaped_table_type},\n                            (\n                                SELECT custom_field_id\n                                FROM ip_custom_fields\n                                WHERE ip_custom_fields.custom_field_column = " . DB::escape($value['column']) . "\n                            ),\n                            {$escaped_column}\n                        )";
                    DB::query($query);
                }
            }
        }
        // Drop old cloumns, and rename new ones
        foreach ($tables as $table) {
            DB::query('DROP TABLE IF EXISTS `ip_' . $table . '_custom`');
            $query = 'RENAME TABLE `ip_' . $table . '_custom_new` TO `ip_' . $table . '_custom`';
            DB::query($query);
        }
        DB::query('ALTER TABLE ip_custom_fields DROP COLUMN custom_field_column');
    }

    /**
     * @originalName upgrade029156
     *
     * @originalFile Setup.php
     */
    public function upgrade029156()
    {
        // The following code will determine if the ip_users table has an existing user_all_clients column
        // If the table already has the column it will be shown in any user query, so get one now
        $test_user = DB::query('SELECT * FROM `ip_users` ORDER BY `user_id` ASC LIMIT 1')->row();
        // Add new user key if applicable
        if ( ! isset($test_user->user_all_clients)) {
            DB::query('ALTER TABLE `ip_users`
              ADD `user_all_clients` INT(1) NOT NULL DEFAULT 0
              AFTER `user_psalt`;');
        }
        // Copy the invoice pdf footer to the new quote pdf footer setting
// TODO: Use dependency injection - $this->load->model('settings/mdl_settings');
        $this->mdl_settings->loadSettings();
// TODO: Laravel autoloads helpers - $this->load->helper('settings');
        $this->mdl_settings->save('pdf_quote_footer', get_setting('pdf_invoice_footer'));
    }

    /**
     * @originalName upgrade03616
     *
     * @originalFile Setup.php
     */
    public function upgrade03616()
    {
        //upgrade the recurring invoices data and replace 0000-00-00 invalid date with null in order to be compliant
        //with the MySQL >= 5.8 defautl SQL Strict mode that is activated by default.
        //migrate the dates data from 0000-00-00 to NULL in order to allow SQL Strict mode. Because the new
        //mysql default mode, the change must be done by PHP logic.
        //**recur_end_date**
        $rows_recur_end_date = DB::query('SELECT * FROM `ip_invoices_recurring`');
        foreach ($rows_recur_end_date->result() as $row) {
            if ($row->recur_end_date == '0000-00-00') {
                DB::set('recur_end_date', null)->where('invoice_recurring_id', $row->invoice_recurring_id)->update('ip_invoices_recurring');
            }
            if ($row->recur_next_date == '0000-00-00') {
                DB::set('recur_next_date', null)->where('invoice_recurring_id', $row->invoice_recurring_id)->update('ip_invoices_recurring');
            }
        }
        //**client_bdate**
        $rows_client_bdate = DB::query('SELECT * FROM `ip_clients`');
        foreach ($rows_client_bdate->result() as $row_bdate) {
            if ($row_bdate->client_birthdate == '0000-00-00') {
                DB::set('client_birthdate', null)->where('client_id', $row_bdate->client_id)->update('ip_clients');
            }
        }
    }

    /**
     * @originalName upgrade039163
     *
     * @originalFile Setup.php
     */
    public function upgrade039163()
    {
        //**Set languages to lowercase & replace include_zugferd setting to einvoicing**
        $einvoicing = '0';
        $step       = 2;
        $rows       = DB::query('SELECT * FROM `ip_settings`');
        foreach ($rows->result() as $row) {
            // Set default_language to lowercase
            if ($row->setting_key == 'default_language') {
                DB::set('setting_value', mb_strtolower($row->setting_value))->where('setting_id', $row->setting_id)->update('ip_settings');
                $step--;
            }
            // include_zugferd > einvoicing
            if ($row->setting_key == 'include_zugferd') {
                $einvoicing = $row->setting_value;
                DB::set('setting_key', 'einvoicing')->where('setting_id', $row->setting_id)->update('ip_settings');
                $step--;
            }
            // All Steps Ok
            if ( ! $step) {
                break;
            }
        }
        // Set all users languages to lowercase
        $rows = DB::query('SELECT * FROM `ip_users`');
        foreach ($rows->result() as $row) {
            if ($row->user_language != 'system') {
                DB::set('user_language', mb_strtolower($row->user_language))->where('user_id', $row->user_id)->update('ip_users');
            }
        }
        if ($einvoicing == '1') {
            // einvoicing is on, Enable Zugferd v1.0 for all clients
            $data = ['client_einvoicing_active' => '1', 'client_einvoicing_version' => 'Zugferdv10'];
            $rows = DB::query('SELECT * FROM `ip_clients`');
            foreach ($rows->result() as $row) {
                if ($row->client_active == '1') {
                    DB::update('ip_clients', $data, ['client_id' => $row->client_id]);
                }
            }
        } else {
            // Delete Zugferd lib & conf
            $filename = 'Zugferdv10';
            $files[]  = APPPATH . 'libraries/XMLtemplates/' . $filename . 'Xml.php';
            $files[]  = APPPATH . 'Helpers/XMLconfigs/' . $filename . '.php';
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * @originalName executeContents
     *
     * @originalFile Setup.php
     */
    private function executeContents(string|bool $contents)
    {
        $commands = explode(';', $contents);
        foreach ($commands as $command) {
            if ( ! mb_trim($command)) {
                continue;
            }
            DB::db_debug = IP_DEBUG;
            DB::query(mb_trim($command) . ';');
            $error = DB::error();
            if ($error['code'] !== 0) {
                $this->errors[] = $error['message'];
            }
        }
    }

    /**
     * @originalName saveVersion
     *
     * @originalFile Setup.php
     */
    private function saveVersion($sql_file)
    {
        $version_db_array = ['version_date_applied' => time(), 'version_file' => $sql_file, 'version_sql_errors' => count($this->errors)];
        DB::insert('ip_versions', $version_db_array);
    }

    /**
     * @originalName installDefaultSettings
     *
     * @originalFile Setup.php
     */
    private function installDefaultSettings()
    {
// TODO: Laravel autoloads helpers - $this->load->helper('string');
        $default_settings = ['default_language' => session('ip_lang'), 'date_format' => 'm/d/Y', 'currency_symbol' => '$', 'currency_symbol_placement' => 'before', 'currency_code' => 'USD', 'invoices_due_after' => 30, 'quotes_expire_after' => 15, 'default_invoice_group' => 3, 'default_quote_group' => 4, 'thousands_separator' => ',', 'decimal_point' => '.', 'cron_key' => random_string('alnum', 16), 'tax_rate_decimal_places' => 2, 'pdf_invoice_template' => 'InvoicePlane', 'pdf_invoice_template_paid' => 'InvoicePlane - paid', 'pdf_invoice_template_overdue' => 'InvoicePlane - overdue', 'pdf_quote_template' => 'InvoicePlane', 'public_invoice_template' => 'InvoicePlane_Web', 'public_quote_template' => 'InvoicePlane_Web', 'disable_sidebar' => 1];
        foreach ($default_settings as $setting_key => $setting_value) {
            DB::where('setting_key', $setting_key);
            if ( ! DB::get('ip_settings')->numRows()) {
                $db_array = ['setting_key' => $setting_key, 'setting_value' => $setting_value];
                DB::insert('ip_settings', $db_array);
            }
        }
    }
}
