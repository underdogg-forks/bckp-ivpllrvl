<?php

namespace App\Helpers;

class RedirectHelper
{
    /**
     * @originalName redirect_to
     *
     * @originalFile redirect_helper.php
     */
    public static function redirectTo($fallback_url_string, $redirect = true)
    {
        $CI           = & get_instance();
        $redirect_url = $CI->session->userdata('redirect_to') ? $CI->session->userdata('redirect_to') : $fallback_url_string;
        $CI->session->unset_userdata('redirect_to');
        if ($redirect) {
            redirect($redirect_url);
        }

        return $redirect_url;
    }

    /**
     * @originalName redirect_to_set
     *
     * @originalFile redirect_helper.php
     */
    public static function redirectToSet(): void
    {
        $CI = & get_instance();
        $CI->session->set_userdata('redirect_to', $CI->uri->uri_string());
    }
}
