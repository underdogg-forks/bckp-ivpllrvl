<?php

namespace App\Helpers;

class UserHelper
{
    /**
     * @originalName format_user
     *
     * @originalFile user_helper.php
     */
    public static function formatUser($user): string
    {
        // GetController an id
        if ($user && is_numeric($user)) {
            $CI = & get_instance();
            if ( ! property_exists($CI, 'mdl_users')) {
                $CI->load->model('users/mdl_users');
            }
            $user = $CI->mdl_users->get_by_id($user);
        }
        // Not exist or find, Stop.
        if (empty($user->user_name)) {
            return '';
        }
        $user_company = empty($user->user_company) ? '' : ' - ' . $user->user_company;
        $contact      = empty($user->user_invoicing_contact) ? '' : ' - ' . $user->user_invoicing_contact;

        return ucfirst($user->user_name) . $user_company . $contact;
    }
}
