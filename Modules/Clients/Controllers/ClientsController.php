<?php

namespace Modules\Clients\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class ClientsController extends AdminController
{
    private const CLIENT_TITLE = 'client_title';

    /**
     * ClientsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_clients');
    }

    /**
     * @originalName index
     *
     * @originalFile ClientsController.php
     */
    public function index(): void
    {
        // Display active clients by default
        redirect()->route('clients/status/active');
    }

    /**
     * @originalName status
     *
     * @originalFile ClientsController.php
     */
    public function status(string $status = 'active', $page = 0): void
    {
        if (is_numeric(array_search($status, ['active', 'inactive'], true))) {
            $function = 'is_' . $status;
            (new ClientsService())->{$function}();
        }
        (new ClientsService())->withTotalBalance()->paginate(site_url('clients/status/' . $status), $page);
        $clients        = (new ClientsService())->result();
        $req_einvoicing = get_setting('einvoicing');
        if ($req_einvoicing) {
            $this->load->helper('e-invoice');
            // eInvoicing++
            foreach ($clients as &$client) {
                // GetController a check of filled Required (client and users) fields for eInvoicing
                $req_einvoicing = get_req_fields_einvoice($client);
                $client         = $this->checkClientEinvoiceActive($client, $req_einvoicing);
            }
            unset($client);
        }

        return view('clients.index', ['records' => $clients, 'filter_display' => true, 'filter_placeholder' => trans('filter_clients'), 'filter_method' => 'filter_clients', 'einvoicing' => get_setting('einvoicing')]);
    }

    /**
     * @originalName form
     *
     * @originalFile ClientsController.php
     */
    public function form($id = null): void
    {
        if ($this->input->post('btn_cancel')) {
            redirect()->route('clients');
        }
        $new_client = false;
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        // Set validation rule based on is_update
        if ($this->input->post('is_update') == 0 && $this->input->post('client_name') != '') {
            $check = $this->db->get_where('ip_clients', ['client_name' => $this->input->post('client_name'), 'client_surname' => $this->input->post('client_surname')])->result();
            if ( ! empty($check)) {
                $this->session->set_flashdata('alert_error', trans('client_already_exists'));
                redirect()->route('clients/form');
            } else {
                $new_client = true;
            }
        }
        if ((new ClientsService())->runValidation()) {
            $client_title_custom = $this->input->post('client_title_custom');
            // Custom title selected
            if ($_POST[self::CLIENT_TITLE] == ClientTitleEnum::CUSTOM) {
                $_POST[self::CLIENT_TITLE] = $client_title_custom;
                (new ClientsService())->setFormValue(self::CLIENT_TITLE, $client_title_custom);
            }
            // fix e-invoice reset
            if ($this->input->post('client_start_einvoicing') == '0') {
                $_POST['client_einvoicing_version'] = '';
                (new ClientsService())->setFormValue('client_einvoicing_version', '');
            }
            $id = (new ClientsService())->save($id);
            if ($new_client) {
                $this->load->model('user_clients/mdl_user_clients');
                (new UserClientsService())->getUsersAllClients();
            }
            $this->load->model('custom_fields/mdl_client_custom');
            $result = (new ClientCustomService())->saveCustom($id, $this->input->post('custom'));
            $where  = 'view';
            if ($result !== true) {
                $this->session->set_flashdata('alert_error', $result);
                $this->session->set_flashdata('alert_success', null);
                $where = 'form';
            }
            redirect('clients/' . $where . '/' . $id);
        }
        $req_einvoicing = get_setting('einvoicing');
        if ($req_einvoicing) {
            $this->load->helper('e-invoice');
            // eInvoicing++
            // GetController a check of filled Required (client and users) fields for eInvoicing
            $req_einvoicing = get_req_fields_einvoice($new_client || ! $id ? null : $this->db->from('ip_clients')->where('client_id', $id)->get()->row());
        }
        if ($id && ! $this->input->post('btn_submit')) {
            if ( ! (new ClientsService())->prepForm($id)) {
                show_404();
            }
            $this->load->model('custom_fields/mdl_client_custom');
            (new ClientsService())->setFormValue('is_update', true);
            $client_custom = (new ClientCustomService())->where('client_id', $id)->get();
            if ($client_custom->numRows()) {
                $client_custom = $client_custom->row();
                unset($client_custom->client_id, $client_custom->client_custom_id);
                foreach ($client_custom as $key => $val) {
                    (new ClientsService())->setFormValue('custom[' . $key . ']', $val);
                }
            }
        } elseif ($this->input->post('btn_submit')) {
            if ($this->input->post('custom')) {
                foreach ($this->input->post('custom') as $key => $val) {
                    (new ClientsService())->setFormValue('custom[' . $key . ']', $val);
                }
            }
        }
        $this->load->model(['custom_fields/mdl_custom_fields', 'custom_values/mdl_custom_values', 'custom_fields/mdl_client_custom']);
        $custom_fields = (new CustomFieldsService())->byTable('ip_client_custom')->get()->result();
        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, (new CustomValuesService())->customValueFields())) {
                $values                                        = (new CustomValuesService())->getByFid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }
        $fields = (new ClientCustomService())->getByClid($id);
        foreach ($custom_fields as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->client_custom_fieldid == $cfield->custom_field_id) {
                    // TODO: Hackish, may need a better optimization
                    (new ClientsService())->setFormValue('custom[' . $cfield->custom_field_id . ']', $fvalue->client_custom_fieldvalue);
                    break;
                }
            }
        }
        $this->load->helper(['custom_values', 'e-invoice']);

        // e-invoice - since 1.6.3
        return view('clients.form', ['client_id' => $id,
            'custom_fields'                      => $custom_fields,
            'custom_values'                      => $custom_values,
            'countries'                          => get_country_list(trans('cldr')),
            'selected_country'                   => (new ClientsService())->formValue('client_country') ?: get_setting('default_country'),
            'languages'                          => get_available_languages(),
            'client_title_choices'               => $this->getClientTitleChoices(),
            'xml_templates'                      => get_xml_template_files(),
            // eInvoicing
            'req_einvoicing' => $req_einvoicing, ]);
    }

    /**
     * @originalName view
     *
     * @originalFile ClientsController.php
     */
    public function view($client_id, $activeTab = 'detail', $page = 0): void
    {
        $client = (new ClientsService())->withTotal()->withTotalBalance()->withTotalPaid()->where('ip_clients.client_id', $client_id)->get()->row();
        if ( ! $client) {
            show_404();
        }
        $this->load->model(['clients/mdl_client_notes', 'invoices/mdl_invoices', 'quotes/mdl_quotes', 'payments/mdl_payments', 'custom_fields/mdl_custom_fields', 'custom_fields/mdl_client_custom']);
        $req_einvoicing = get_setting('einvoicing');
        if ($req_einvoicing) {
            $this->load->helper('e-invoice');
            // eInvoicing++
            // GetController a check of filled Required (client and users) fields for eInvoicing
            $req_einvoicing = get_req_fields_einvoice($client);
            $client         = $this->checkClientEinvoiceActive($client, $req_einvoicing);
        }
        // Change page only for one url (tab) system
        $p = ['invoices' => 0, 'quotes' => 0, 'payments' => 0];
        // Default
        // Session key
        $key = 'clientview';
        // When detail (from menu)
        if ($activeTab == 'detail') {
            // Clear temp + session
            $this->session->unmark_temp($key);
            unset($_SESSION[$key]);
        } else {
            // Set pages saved in session
            if (isset($_SESSION[$key])) {
                $p = $_SESSION[$key];
            }
            // Up Actual page num
            $p[$activeTab] = $page;
            // Save in session
            $_SESSION[$key] = $p;
            // For 300 seconds
            $this->session->mark_as_temp($key);
        }
        $base_url = site_url('clients/view/' . $client_id);
        (new InvoicesService())->byClient($client_id)->paginate($base_url . '/invoices', $p['invoices'], 5);
        (new QuotesService())->byClient($client_id)->paginate($base_url . '/quotes', $p['quotes'], 5);
        (new PaymentsService())->byClient($client_id)->paginate($base_url . '/payments', $p['payments'], 5);
        $custom_fields = (new ClientCustomService())->getByClient($client_id)->result();
        (new ClientCustomService())->prepForm($client_id);
        $this->layout->set(['client' => $client, 'client_notes' => (new ClientNotesService())->where('client_id', $client_id)->get()->result(), 'invoices' => (new InvoicesService())->result(), 'quotes' => (new QuotesService())->result(), 'payments' => (new PaymentsService())->result(), 'custom_fields' => $custom_fields, 'quote_statuses' => (new QuotesService())->statuses(), 'invoice_statuses' => (new InvoicesService())->statuses(), 'activeTab' => $activeTab, 'req_einvoicing' => $req_einvoicing]);
        $this->layout->buffer([['invoice_table', 'invoices/partial_invoice_table'], ['quote_table', 'quotes/partial_quote_table'], ['payment_table', 'payments/partial_payments_table'], ['partial_notes', 'clients/partial_notes'], ['content', 'clients/view']]);
        $this->layout->render();
    }

    /**
     * @originalName delete
     *
     * @originalFile ClientsController.php
     */
    public function delete($client_id): void
    {
        (new ClientsService())->delete($client_id);
        redirect()->route('clients');
    }

    /**
     * @originalName getClientTitleChoices
     *
     * @originalFile ClientsController.php
     */
    private function getClientTitleChoices(): array
    {
        return array_map(fn ($clientTitleEnum) => $clientTitleEnum->value, ClientTitleEnum::cases());
    }

    /**
     * @originalName checkClientEinvoiceActive
     *
     * @originalFile ClientsController.php
     */
    private function checkClientEinvoiceActive($client, $req_einvoicing)
    {
        // Update active eInvoicing client
        $o = $client->client_einvoicing_active;
        if ( ! empty($client->client_einvoicing_version) && $req_einvoicing->clients[$client->client_id]->einvoicing_empty_fields == 0) {
            $client->client_einvoicing_active = 1;
            // update view
        } else {
            $client->client_einvoicing_active = 0;
            // update view
        }
        // Update db if need
        if ($o != $client->client_einvoicing_active) {
            $this->db->where('client_id', $client->client_id);
            $this->db->set('client_einvoicing_active', $client->client_einvoicing_active);
            $this->db->update('ip_clients');
        }

        return $client;
    }
}
