<?php

namespace Modules\Settings\Controllers;

use Illuminate\Support\Facades\Log;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class SettingsController extends AdminController
{
    /**
     * SettingsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_settings');
        $this->load->library('crypt');
        $this->load->library('form_validation');
        $this->load->helper('payments_helper');
    }

    /**
     * @originalName index
     *
     * @originalFile SettingsController.php
     */
    public function index()
    {
        // GetController the payment gateway configurations
        $this->config->load('payment_gateways');
        $gateways = $this->config->item('payment_gateways');
        // GetController the number formats configurations
        $this->config->load('number_formats');
        $number_formats = $this->config->item('number_formats');
        // Save input if request is POSt
        if ($this->input->post('settings')) {
            $settings = $this->input->post('settings');
            // Only execute if the setting is different
            if ($settings['tax_rate_decimal_places'] != get_setting('tax_rate_decimal_places')) {
                $this->db->query("\n                    ALTER TABLE `ip_tax_rates` CHANGE `tax_rate_percent` `tax_rate_percent`\n                    DECIMAL( 5, {$settings['tax_rate_decimal_places']} ) NOT null");
            }
            // Save the submitted settings :todo:improve: Save In One SQL query : $db_array[$key] = val; •••& @end mdl save $db_array.
            foreach ($settings as $key => $value) {
                if (str_contains($key, 'field_is_password') || str_contains($key, 'field_is_amount')) {
                    // Skip all meta fields
                    continue;
                }
                if (isset($settings[$key . '_field_is_password']) && empty($value)) {
                    // Password field, but empty value, let's skip it
                    continue;
                }
                if (isset($settings[$key . '_field_is_password']) && $value != '') {
                    // Encrypt passwords but don't save empty passwords
                    (new SettingsService())->save($key, $this->crypt->encode(mb_trim($value)));
                } elseif (isset($settings[$key . '_field_is_amount'])) {
                    // Format amount inputs
                    (new SettingsService())->save($key, standardize_amount($value));
                } else {
                    (new SettingsService())->save($key, $value);
                }
                if ($key == 'number_format') {
                    // Set thousands_separator and decimal_point according to number_format
                    (new SettingsService())->save('decimal_point', $number_formats[$value]['decimal_point']);
                    (new SettingsService())->save('thousands_separator', $number_formats[$value]['thousands_separator']);
                }
            }
            $upload_config = [
                'upload_path'   => './uploads/',
                'allowed_types' => 'gif|jpg|jpeg|png|svg',
                // Invoice quote logo image :Todo: Add webp avif? (& test imgs in pdf)
                'max_size'   => '9999',
                'max_width'  => '9999',
                'max_height' => '9999',
            ];
            // Check for invoice logo upload
            if ($_FILES['invoice_logo']['name']) {
                $this->load->library('upload', $upload_config);
                if ( ! $this->upload->do_upload('invoice_logo')) {
                    $this->session->set_flashdata('alert_error', $this->upload->display_errors());
                    redirect()->route('settings');
                }
                $upload_data = $this->upload->data();
                (new SettingsService())->save('invoice_logo', $upload_data['file_name']);
            }
            // Check for login logo upload
            if ($_FILES['login_logo']['name']) {
                $this->load->library('upload', $upload_config);
                if ( ! $this->upload->do_upload('login_logo')) {
                    $this->session->set_flashdata('alert_error', $this->upload->display_errors());
                    redirect()->route('settings');
                }
                $upload_data = $this->upload->data();
                (new SettingsService())->save('login_logo', $upload_data['file_name']);
            }
            $this->session->set_flashdata('alert_success', trans('settings_successfully_saved'));
            redirect()->route('settings');
        }
        // Load required resources
        $this->load->model(['invoice_groups/mdl_invoice_groups', 'tax_rates/mdl_tax_rates', 'email_templates/mdl_email_templates', 'payment_methods/mdl_payment_methods', 'invoices/mdl_templates', 'custom_fields/mdl_invoice_custom', 'custom_fields/mdl_custom_fields']);
        // Collect the list of templates
        $pdf_invoice_templates    = (new TemplatesService())->getInvoiceTemplates('pdf');
        $public_invoice_templates = (new TemplatesService())->getInvoiceTemplates('public');
        $pdf_quote_templates      = (new TemplatesService())->getQuoteTemplates('pdf');
        $public_quote_templates   = (new TemplatesService())->getQuoteTemplates('public');
        // GetController all themes
        $available_themes = (new SettingsService())->getThemes();
        // Set data in the layout
        return view('settings.index', ['invoice_groups' => (new InvoiceGroupsService())->get()->result(), 'tax_rates' => (new TaxRatesService())->get()->result(), 'payment_methods' => (new PaymentMethodsService())->get()->result(), 'public_invoice_templates' => $public_invoice_templates, 'pdf_invoice_templates' => $pdf_invoice_templates, 'public_quote_templates' => $public_quote_templates, 'pdf_quote_templates' => $pdf_quote_templates, 'languages' => get_available_languages(), 'countries' => get_country_list(trans('cldr')), 'date_formats' => date_formats(), 'current_date' => new DateTime(), 'available_themes' => $available_themes, 'email_templates_quote' => (new EmailTemplatesService())->where('email_template_type', 'quote')->get()->result(), 'email_templates_invoice' => (new EmailTemplatesService())->where('email_template_type', 'invoice')->get()->result(), 'custom_fields' => ['ip_invoice_custom' => (new CustomFieldsService())->byTable('ip_invoice_custom')->get()->result()], 'gateway_drivers' => $gateways, 'number_formats' => $number_formats, 'gateway_currency_codes' => get_currencies(), 'first_days_of_weeks' => ['0' => lang('sunday'), '1' => lang('monday')], 'legacy_calculation' => config_item('legacy_calculation')]);
    }

    /**
     * @originalName removeLogo
     *
     * @originalFile SettingsController.php
     */
    public function removeLogo(string $type)
    {
        unlink('./uploads/' . get_setting($type . '_logo'));
        (new SettingsService())->save($type . '_logo', '');
        $this->session->set_flashdata('alert_success', lang($type . '_logo_removed'));
        redirect()->route('settings');
    }
}
