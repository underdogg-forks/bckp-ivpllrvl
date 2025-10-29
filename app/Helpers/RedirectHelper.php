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
        $redirect_url = session('redirect_to', $fallback_url_string);
        session()->forget('redirect_to');
        if ($redirect) {
            return redirect($redirect_url);
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
        session(['redirect_to' => request()->path()]);
    }
}
