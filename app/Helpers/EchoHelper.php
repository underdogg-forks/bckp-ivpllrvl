<?php

namespace App\Helpers;

class EchoHelper
{
    /**
     * @originalName htmlsc
     *
     * @originalFile echo_helper.php
     */
    public static function htmlsc($output): ?string
    {
        if (null !== $output) {
            return htmlspecialchars($output, ENT_QUOTES | ENT_IGNORE);
        }

        return $output;
    }

    /**
     * @originalName _htmlsc
     *
     * @originalFile echo_helper.php
     */
    public static function Htmlsc($output)
    {
        if ($output == null) {
            return '';
        }
        echo htmlspecialchars($output, ENT_QUOTES | ENT_IGNORE);
    }

    /**
     * @originalName _htmle
     *
     * @originalFile echo_helper.php
     */
    public static function Htmle($output)
    {
        if ($output == null) {
            return '';
        }
        echo htmlentities($output, ENT_COMPAT);
    }

    /**
     * @originalName _trans
     *
     * @originalFile echo_helper.php
     */
    public static function Trans($line, $id = '', $default = null): void
    {
        echo trans($line, $id, $default);
    }

    /**
     * @originalName _auto_link
     *
     * @originalFile echo_helper.php
     */
    public static function AutoLink($str, $type = 'both', $popup = false): void
    {
        echo auto_link(htmlsc($str), $type, $popup);
    }

    /**
     * @originalName _csrf_field
     *
     * @originalFile echo_helper.php
     */
    public static function CsrfField(): void
    { // TODO: Replace with Laravel patterns
        echo '<input type="hidden" name="' . config('csrf_token_name');
        echo '" value="' . $CI->security->get_csrf_hash() . '">';
    }

    /**
     * @originalName _theme_asset
     *
     * @originalFile echo_helper.php
     */
    public static function ThemeAsset($asset): void
    {
        $asset = IP_DEBUG ? strtr($asset, ['.min.' => '.']) : $asset;
        echo base_url() . 'assets/' . get_setting('system_theme', 'invoiceplane');
        echo '/' . $asset . '?v=' . get_setting('current_version');
    }

    /**
     * @originalName _core_asset
     *
     * @originalFile echo_helper.php
     */
    public static function CoreAsset($asset): void
    {
        $asset = IP_DEBUG ? strtr($asset, ['.min.' => '.']) : $asset;
        echo base_url() . 'assets/core/' . $asset . '?v=' . get_setting('current_version');
    }
}
