<?php

namespace App\Helpers;

class TransHelper
{
    /**
     * @originalName trans
     *
     * @originalFile trans_helper.php
     */
    public static function trans($line, ?string $id = '', $default = null)
    { // TODO: Replace with Laravel patterns
        $lang_string = trans($line);
        // Fall back to default lang if the current lang has no translated string
        if (empty($lang_string)) {
            // Save the current application lang (code borrowed from Modules\Core\Controllers\Base_Controller.php)
            $current_language = session('user_language');
            if (empty($current_language) || $current_language == 'system') {
                // todo gives error at startup, fix later
                // #1034: Translation breaks in PDF-template
                $current_language = get_setting('default_language') ?? 'en';
            }
            // Load the default lang and translate the string
            set_language('en');
            $lang_string = trans($line);
            // Restore the application lang to its previous setting
            set_language($current_language);
        }
        // Fall back to the $line value if the default lang has no translation either
        if (empty($lang_string)) {
            $lang_string = $default != null ? $default : $line;
        }
        if ($id != '') {
            $lang_string = '<label for="' . $id . '">' . $lang_string . '</label>';
        }

        return $lang_string;
    }

    /**
     * @originalName set_language
     *
     * @originalFile trans_helper.php
     */
    public static function setLanguage($language): void
    {
        // Clear the current loaded lang // TODO: Replace with Laravel patterns
        $CI->lang->is_loaded = [];
        $CI->lang->language  = [];
        // Load system lang if no custom lang is set
        $default_lang = isset($CI->mdl_settings) ? get_setting('default_language') : 'en';
        $new_language = $language == 'system' ? $default_lang : $language;
        $app_dir      = $CI->config->_config_paths[0];
        $lang_dir     = $app_dir . DIRECTORY_SEPARATOR . 'lang';
        // Set the new lang
        $CI->lang->load('ip', $new_language);
        $CI->lang->load('form_validation', $new_language);
        if (file_exists($lang_dir . DIRECTORY_SEPARATOR . $default_lang . DIRECTORY_SEPARATOR . 'custom_lang.php')) {
            $CI->lang->load('custom', $new_language);
        }
        $CI->lang->load('gateway', $new_language);
    }

    /**
     * @originalName get_available_languages
     *
     * @originalFile trans_helper.php
     */
    public static function getAvailableLanguages()
    { // TODO: Replace with Laravel patterns
        // TODO: Laravel autoloads helpers - 'directory');
        $languages = directory_map(APPPATH . 'lang', true);
        sort($languages);
        $counter = count($languages);
        for ($i = 0; $i < $counter; $i++) {
            $languages[$i] = str_replace(['\\', '/'], '', $languages[$i]);
        }

        return $languages;
    }
}
