<?php

namespace Modules\Core\Controllers;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class AdminController extends UserController
{
    public function __construct()
    {
        parent::__construct('user_type', 1);
        $this->setCacheHeaders();
    }

    /**
     * @originalName filter_input
     *
     * @originalFile AdminController.php
     */
    protected function filterInput(): void
    {
        $input = request()->post();
        array_walk($input, function (&$value, $key): void {
            if ( ! is_array($value)) {
                $value = strip_tags($value);
                $value = e($value);
            }
        });
    }

    /**
     * @originalName setCacheHeaders
     *
     * @originalFile AdminController.php
     */
    protected function setCacheHeaders()
    {
        $xFrameOptions = env('X_FRAME_OPTIONS');
        if ( ! empty($xFrameOptions)) {
            header('X-Frame-Options: ' . $xFrameOptions);
        }
        if (env_bool('ENABLE_X_CONTENT_TYPE_OPTIONS', 'true')) {
            header('X-Content-Type-Options: nosniff');
        }
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    }
}
