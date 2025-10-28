<?php

namespace Modules\Sessions\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\Users\Models\User;

#[AllowDynamicProperties]
class SessionsService extends BaseService
{
    /**
     * Authenticate a user by email and password, upgrade legacy MD5 password hashes when needed, and initialize session data on success.
     *
     * If the user record contains an old MD5 password, a successful MD5 verification will replace it with the current salted hash format before continuing.
     *
     * @param string $email    the user's email address used to locate the account
     * @param string $password the plaintext password to verify
     *
     * @return bool `true` if authentication succeeds and session data is set (session contains `user_type`, `user_id`, `user_name`, `user_email`, `user_company`, and `user_language`), `false` otherwise
     */
    public function auth($email, $password)
    {
        $user = User::query()->where('user_email', $email)->first();

        if ($user) {
            $this->load->library('crypt');
            /*
             * Password hashing changed after 1.2.0
             * Check to see if user has logged in since the password change
             */
            if ( ! $user->user_psalt) {
                /*
                 * The user has not logged in, so we're going to attempt to
                 * update their record with the updated hash
                 */
                if (md5($password) == $user->user_password) {
                    /**
                     * The md5 login validated - let's update this user
                     * to the new hash.
                     */
                    $salt     = $this->crypt->salt();
                    $hash     = $this->crypt->generate_password($password, $salt);
                    $db_array = ['user_psalt' => $salt, 'user_password' => $hash];

                    User::query()->where('user_id', $user->user_id)->update($db_array);
                    $user = User::query()->where('user_email', $email)->first();
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
