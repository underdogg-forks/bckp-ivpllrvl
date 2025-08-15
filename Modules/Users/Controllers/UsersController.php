<?php

namespace Modules\Users\Controllers;

use Illuminate\Support\Facades\Log;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class UsersController extends AdminController
{
    /**
     * UsersController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_users');
    }

    /**
     * @originalName index
     *
     * @originalFile UsersController.php
     */
    public function index($page = 0)
    {
        (new UsersService())->paginate(site_url('users/index'), $page);
        $users = (new UsersService())->result();
        return view('users.index', ['filter_display' => true, 'filter_placeholder' => trans('filter_users'), 'filter_method' => 'filter_users', 'users' => $users, 'user_types' => (new UsersService())->userTypes()]);
    }

    /**
     * @originalName form
     *
     * @originalFile UsersController.php
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect()->route('users');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ((new UsersService())->runValidation($id ? 'validation_rules_existing' : 'validation_rules')) {
            $id = (new UsersService())->save($id);
            $this->load->model('custom_fields/mdl_user_custom');
            (new UserCustomService())->saveCustom($id, $this->input->post('custom'));
            // Update the session details if the logged in user edited his account
            if ($this->session->userdata('user_id') == $id) {
                $new_details  = (new UsersService())->getById($id);
                $session_data = ['user_type' => $new_details->user_type, 'user_id' => $new_details->user_id, 'user_name' => $new_details->user_name, 'user_email' => $new_details->user_email, 'user_company' => $new_details->user_company, 'user_language' => $new_details->user_language ?? 'system'];
                $this->session->set_userdata($session_data);
            }
            $this->session->unset_userdata('user_clients');
            redirect()->route('users');
        }
        if ($id && ! $this->input->post('btn_submit')) {
            if ( ! (new UsersService())->prepForm($id)) {
                show_404();
            }
            $this->load->model('custom_fields/mdl_user_custom');
            $user_custom = (new UserCustomService())->where('user_id', $id)->get();
            if ($user_custom->numRows()) {
                $user_custom = $user_custom->row();
                unset($user_custom->user_id, $user_custom->user_custom_id);
                foreach ($user_custom as $key => $val) {
                    (new UsersService())->setFormValue('custom[' . $key . ']', $val);
                }
            }
        } elseif ($this->input->post('btn_submit')) {
            if ($this->input->post('custom')) {
                foreach ($this->input->post('custom') as $key => $val) {
                    (new UsersService())->setFormValue('custom[' . $key . ']', $val);
                }
            }
        }
        $this->load->helper(['custom_values', 'e-invoice']);
        $this->load->model(['user_clients/mdl_user_clients', 'clients/mdl_clients', 'custom_fields/mdl_custom_fields', 'custom_fields/mdl_user_custom', 'custom_values/mdl_custom_values']);
        $custom_fields['ip_user_custom'] = (new CustomFieldsService())->byTable('ip_user_custom')->get()->result();
        $custom_values                   = [];
        foreach ($custom_fields['ip_user_custom'] as $custom_field) {
            if (in_array($custom_field->custom_field_type, (new CustomValuesService())->customValueFields())) {
                $values                                        = (new CustomValuesService())->getByFid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }
        $fields = (new UserCustomService())->getByUseid($id);
        foreach ($custom_fields['ip_user_custom'] as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->user_custom_fieldid == $cfield->custom_field_id) {
                    // TODO: Hackish, may need a better optimization
                    (new UsersService())->setFormValue('custom[' . $cfield->custom_field_id . ']', $fvalue->user_custom_fieldvalue);
                    break;
                }
            }
        }
        // Need in remittance text tags selector (template-tags-invoices)
        $custom_fields['ip_invoice_custom'] = (new CustomFieldsService())->byTable('ip_invoice_custom')->get()->result();
        return view('users.form', ['id' => $id, 'user_types' => (new UsersService())->userTypes(), 'user_clients' => (new UserClientsService())->where('ip_user_clients.user_id', $id)->get()->result(), 'custom_fields' => $custom_fields, 'custom_values' => $custom_values, 'countries' => get_country_list(trans('cldr')), 'selected_country' => (new UsersService())->formValue('user_country') ?: get_setting('default_country'), 'clients' => (new ClientsService())->where('client_active', 1)->get()->result(), 'languages' => get_available_languages(), 'einvoicing' => get_setting('einvoicing')]);
    }

    /**
     * @originalName changePassword
     *
     * @originalFile UsersController.php
     */
    public function changePassword(string $user_id)
    {
        if ($this->input->post('btn_cancel')) {
            redirect()->route('users');
        }
        if ((new UsersService())->runValidation('validation_rules_change_password')) {
            (new UsersService())->saveChangePassword($user_id, $this->input->post('user_password'));
            redirect('users/form/' . $user_id);
        }
        $this->layout->buffer('content', 'users/form_change_password');
        $this->layout->render();
    }

    /**
     * @originalName delete
     *
     * @originalFile UsersController.php
     */
    public function delete($id)
    {
        if ($id != 1) {
            (new UsersService())->delete($id);
        }
        redirect()->route('users');
    }

    /**
     * @originalName deleteUserClient
     *
     * @originalFile UsersController.php
     */
    public function deleteUserClient(string $user_id, $user_client_id)
    {
        $this->load->model('mdl_user_clients');
        (new UserClientsService())->delete($user_client_id);
        redirect('users/form/' . $user_id);
    }
}
