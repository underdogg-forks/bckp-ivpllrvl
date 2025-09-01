<?php

namespace App\Helpers;

class SettingsHelper
{
    /**
     * @originalName get_setting
     *
     * @originalFile settings_helper.php
     */
    public static function getSetting($setting_key, $default = '', $escape = false)
    {
        $CI    = & get_instance();
        $value = $CI->mdl_settings->setting($setting_key, $default);

        return $escape ? htmlsc($value) : $value;
    }

    /**
     * @originalName get_gateway_settings
     *
     * @originalFile settings_helper.php
     */
    public static function getGatewaySettings($gateway)
    {
        $CI = & get_instance();

        return $CI->mdl_settings->gateway_settings($gateway);
    }

    /**
     * @originalName check_select
     *
     * @originalFile settings_helper.php
     */
    public static function checkSelect($value1, $value2 = null, $operator = '==', $checked = false): void
    {
        $select = $checked ? 'checked="checked"' : 'selected="selected"';
        // Instant-validate if $value1 is a bool value
        if (is_bool($value1) && $value2 === null) {
            echo $value1 ? $select : '';

            return;
        }
        switch ($operator) {
            case '==':
                $echo_selected = $value1 == $value2;
                break;
            case '!=':
                $echo_selected = $value1 != $value2;
                break;
            case 'e':
            case '!e':
                $echo_selected = empty($value1);
                break;
            default:
                $echo_selected = (bool) $value1;
                break;
        }
        echo $echo_selected ? $select : '';
    }
}
