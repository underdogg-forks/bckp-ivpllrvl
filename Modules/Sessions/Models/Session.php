<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Sessions\Models;

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */
#[AllowDynamicProperties]
class Session extends BaseModel
{
    /**
     * @originalName auth
     *
     * @originalFile Session.php
     */
    public function auth($email, $password)
    {
        $this->db->where('user_email', $email);
        $query = $this->db->get('ip_users');
        if ($query->numRows()) {
            $user = $query->row();
            $this->load->library('crypt');
            /*
             * Password hashing changed after 1.2.0
             * Check to see if user has logged in since the password change
             */
            if (!$user->user_psalt) {
                /*
                 * The user has not logged in, so we're going to attempt to
                 * update their record with the updated hash
                 */
                if (md5($password) == $user->user_password) {
                    /**
                     * The md5 login validated - let's update this user
                     * to the new hash.
                     */
                    $salt = $this->crypt->salt();
                    $hash = $this->crypt->generate_password($password, $salt);
                    $db_array = ['user_psalt' => $salt, 'user_password' => $hash];
                    $this->db->where('user_id', $user->user_id);
                    $this->db->update('ip_users', $db_array);
                    $this->db->where('user_email', $email);
                    $user = $this->db->get('ip_users')->row();
                } else {
                    // The password didn't verify against original md5
                    return false;
                }
            }
            if ($this->crypt->check_password($user->user_password, $password)) {
                $session_data = ['user_type' => $user->user_type, 'user_id' => $user->user_id, 'user_name' => $user->user_name, 'user_email' => $user->user_email, 'user_company' => $user->user_company, 'user_language' => $user->user_language ?? 'system'];
                $this->session->set_userdata($session_data);
                return true;
            }
        }
        return false;
    }
}
