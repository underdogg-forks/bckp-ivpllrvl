<?php

namespace Modules\Core\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use src\Models\User;

#[AllowDynamicProperties]
class UsersService extends BaseService
{
    public $table = 'ip_users';

    public $primary_key = 'ip_users.user_id';

    public $date_created_field = 'user_date_created';

    public $date_modified_field = 'user_date_modified';

    /**
     * @originalName userTypes
     *
     * @originalFile User.php
     */
    public function userTypes()
    {
        return ['1' => trans('administrator'), '2' => trans('guest_read_only')];
    }

    /**
     * @originalName defaultSelect
     *
     * @originalFile User.php
     */
    public function defaultSelect(): void
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_users.*', false);
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile User.php
     */
    public function defaultOrderBy(): void
    {
        $this->db->orderBy('ip_users.user_name');
    }

    /**
     * @originalName validationRules
     *
     * @originalFile User.php
     */
    public function validationRules()
    {
        return [
            'user_type'              => ['field' => 'user_type', 'label' => trans('user_type'), 'rules' => 'required'],
            'user_email'             => ['field' => 'user_email', 'label' => trans('email'), 'rules' => 'required|valid_email|is_unique[ip_users.user_email]'],
            'user_name'              => ['field' => 'user_name', 'label' => trans('name'), 'rules' => 'required'],
            'user_password'          => ['field' => 'user_password', 'label' => trans('password'), 'rules' => 'required|min_length[8]'],
            'user_passwordv'         => ['field' => 'user_passwordv', 'label' => trans('verify_password'), 'rules' => 'required|matches[user_password]'],
            'user_language'          => ['field' => 'user_language', 'label' => trans('lang'), 'rules' => 'required'],
            'user_company'           => ['field' => 'user_company'],
            'user_address_1'         => ['field' => 'user_address_1'],
            'user_address_2'         => ['field' => 'user_address_2'],
            'user_city'              => ['field' => 'user_city'],
            'user_state'             => ['field' => 'user_state'],
            'user_zip'               => ['field' => 'user_zip'],
            'user_country'           => ['field' => 'user_country', 'label' => trans('country')],
            'user_phone'             => ['field' => 'user_phone'],
            'user_fax'               => ['field' => 'user_fax'],
            'user_mobile'            => ['field' => 'user_mobile'],
            'user_web'               => ['field' => 'user_web'],
            'user_vat_id'            => ['field' => 'user_vat_id'],
            'user_tax_code'          => ['field' => 'user_tax_code'],
            'user_invoicing_contact' => ['field' => 'user_invoicing_contact', 'rules' => 'trim'],
            'user_bank'              => ['field' => 'user_bank', 'rules' => 'trim'],
            'user_iban'              => ['field' => 'user_iban'],
            'user_bic'               => ['field' => 'user_bic', 'rules' => 'trim|xss_clean'],
            'user_remittance_text'   => ['field' => 'user_remittance', 'rules' => 'trim|xss_clean'],
            // SUMEX
            'user_subscribernumber' => ['field' => 'user_subscribernumber'],
            'user_gln'              => ['field' => 'user_gln'],
            'user_rcc'              => ['field' => 'user_rcc'],
        ];
    }

    /**
     * @originalName getLatest
     *
     * @originalFile User.php
     */
    public function getLatest($amount = 20)
    {
        return $this->mdl_users->where('user_active', 1)->orderBy('user_id', 'DESC')->limit($amount)->get()->result();
    }

    /**
     * @originalName validationRulesExisting
     *
     * @originalFile User.php
     */
    public function validationRulesExisting()
    {
        return [
            'user_type'              => ['field' => 'user_type', 'label' => trans('user_type'), 'rules' => 'required'],
            'user_email'             => ['field' => 'user_email', 'label' => trans('email'), 'rules' => 'required|valid_email'],
            'user_name'              => ['field' => 'user_name', 'label' => trans('name'), 'rules' => 'required'],
            'user_language'          => ['field' => 'user_language', 'label' => trans('lang'), 'rules' => 'required'],
            'user_company'           => ['field' => 'user_company'],
            'user_address_1'         => ['field' => 'user_address_1'],
            'user_address_2'         => ['field' => 'user_address_2'],
            'user_city'              => ['field' => 'user_city'],
            'user_state'             => ['field' => 'user_state'],
            'user_zip'               => ['field' => 'user_zip'],
            'user_country'           => ['field' => 'user_country', 'label' => trans('country')],
            'user_phone'             => ['field' => 'user_phone'],
            'user_fax'               => ['field' => 'user_fax'],
            'user_mobile'            => ['field' => 'user_mobile'],
            'user_web'               => ['field' => 'user_web'],
            'user_vat_id'            => ['field' => 'user_vat_id'],
            'user_tax_code'          => ['field' => 'user_tax_code'],
            'user_invoicing_contact' => ['field' => 'user_invoicing_contact'],
            'user_bank'              => ['field' => 'user_bank'],
            'user_iban'              => ['field' => 'user_iban'],
            'user_bic'               => ['field' => 'user_bic'],
            'user_remittance_text'   => ['field' => 'user_remittance'],
            // SUMEX
            'user_subscribernumber' => ['field' => 'user_subscribernumber'],
            'user_gln'              => ['field' => 'user_gln'],
            'user_rcc'              => ['field' => 'user_rcc'],
        ];
    }

    /**
     * @originalName validationRulesChangePassword
     *
     * @originalFile User.php
     */
    public function validationRulesChangePassword()
    {
        return ['user_password' => ['field' => 'user_password', 'label' => trans('password'), 'rules' => 'required'], 'user_passwordv' => ['field' => 'user_passwordv', 'label' => trans('verify_password'), 'rules' => 'required|matches[user_password]']];
    }

    /**
     * @originalName dbArray
     *
     * @originalFile User.php
     */
    public function dbArray()
    {
        $db_array = parent::dbArray();
        if (isset($db_array['user_password'])) {
            unset($db_array['user_passwordv']);
            $this->load->library('crypt');
            $user_psalt                = $this->crypt->salt();
            $db_array['user_psalt']    = $user_psalt;
            $db_array['user_password'] = $this->crypt->generate_password($db_array['user_password'], $user_psalt);
        }

        return $db_array;
    }

    /**
     * Update the stored password and salt for the given user and set a success flash message.
     *
     * Updates the user's password hash and salt in persistent storage for the user with the
     * specified identifier, then sets a session flash message indicating the password change.
     *
     * @param int    $user_id  the identifier of the user to update
     * @param string $password the new plaintext password to be salted and hashed before storage
     */
    public function saveChangePassword($user_id, $password): void
    {
        $this->load->library('crypt');
        $user_psalt    = $this->crypt->salt();
        $user_password = $this->crypt->generate_password($password, $user_psalt);
        $db_array      = ['user_psalt' => $user_psalt, 'user_password' => $user_password];

        User::query()->where('user_id', $user_id)->update($db_array);
        $this->session->set_flashdata('alert_success', trans('password_changed'));
    }

    /**
     * @originalName save
     *
     * @originalFile User.php
     */
    public function save($id = null, $db_array = null)
    {
        $id = parent::save($id, $db_array);
        if ($user_clients = $this->session->userdata('user_clients')) {
            $this->load->model('users/mdl_user_clients');
            foreach ($user_clients as $user_client) {
                $this->mdl_user_clients->save(null, ['user_id' => $id, 'client_id' => $user_client]);
            }
            $this->session->unset_userdata('user_clients');
        }

        return $id;
    }

    /**
     * @originalName delete
     *
     * @originalFile User.php
     */
    public function delete($id): void
    {
        parent::delete($id);
        $this->load->helper('orphan');
        delete_orphans();
    }
}
