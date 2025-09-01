<?php

namespace App\Helpers;

class DateHelper
{
    /**
     * @originalName date_formats
     *
     * @originalFile date_helper.php
     */
    public static function dateFormats(): array
    {
        return ['d/m/Y' => ['setting' => 'd/m/Y', 'datepicker' => 'dd/mm/yyyy'], 'd-m-Y' => ['setting' => 'd-m-Y', 'datepicker' => 'dd-mm-yyyy'], 'd-M-Y' => ['setting' => 'd-M-Y', 'datepicker' => 'dd-M-yyyy'], 'd.m.Y' => ['setting' => 'd.m.Y', 'datepicker' => 'dd.mm.yyyy'], 'j.n.Y' => ['setting' => 'j.n.Y', 'datepicker' => 'd.m.yyyy'], 'd M,Y' => ['setting' => 'd M,Y', 'datepicker' => 'dd M,yyyy'], 'm/d/Y' => ['setting' => 'm/d/Y', 'datepicker' => 'mm/dd/yyyy'], 'm-d-Y' => ['setting' => 'm-d-Y', 'datepicker' => 'mm-dd-yyyy'], 'm.d.Y' => ['setting' => 'm.d.Y', 'datepicker' => 'mm.dd.yyyy'], 'Y/m/d' => ['setting' => 'Y/m/d', 'datepicker' => 'yyyy/mm/dd'], 'Y-m-d' => ['setting' => 'Y-m-d', 'datepicker' => 'yyyy-mm-dd'], 'Y.m.d' => ['setting' => 'Y.m.d', 'datepicker' => 'yyyy.mm.dd']];
    }

    /**
     * @originalName date_from_mysql
     *
     * @originalFile date_helper.php
     */
    public static function dateFromMysql($date, $ignore_post_check = false)
    {
        if ($date) {
            if ( ! $_POST || $ignore_post_check) {
                $CI = & get_instance();
                if ($date != null) {
                    $date = DateTime::createFromFormat('Y-m-d', $date);

                    return $date->format($CI->mdl_settings->setting('date_format'));
                }

                return '';
            }

            return $date;
        }

        return '';
    }

    /**
     * @originalName date_from_timestamp
     *
     * @originalFile date_helper.php
     */
    public static function dateFromTimestamp($timestamp): string
    {
        $CI   = & get_instance();
        $date = new DateTime();
        $date->setTimestamp($timestamp);

        return $date->format($CI->mdl_settings->setting('date_format'));
    }

    /**
     * @originalName date_to_mysql
     *
     * @originalFile date_helper.php
     */
    public static function dateToMysql($date)
    {
        $CI = & get_instance();
        $d  = DateTime::createFromFormat($CI->mdl_settings->setting('date_format'), $date);

        return $d ? $d->format('Y-m-d') : '';
    }

    /**
     * @originalName is_date
     *
     * @originalFile date_helper.php
     */
    public static function isDate($date): bool
    {
        $CI     = & get_instance();
        $format = $CI->mdl_settings->setting('date_format');
        $d      = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }

    /**
     * @originalName date_format_setting
     *
     * @originalFile date_helper.php
     */
    public static function dateFormatSetting()
    {
        $CI           = & get_instance();
        $date_format  = $CI->mdl_settings->setting('date_format');
        $date_formats = date_formats();

        return $date_formats[$date_format]['setting'];
    }

    /**
     * @originalName date_format_datepicker
     *
     * @originalFile date_helper.php
     */
    public static function dateFormatDatepicker()
    {
        $CI           = & get_instance();
        $date_format  = $CI->mdl_settings->setting('date_format');
        $date_formats = date_formats();

        return $date_formats[$date_format]['datepicker'];
    }

    /**
     * @originalName increment_user_date
     *
     * @originalFile date_helper.php
     */
    public static function incrementUserDate($date, string $increment): string
    {
        if ( ! $d = date_to_mysql($date)) {
            return '';
        }
        $new_date = new DateTime($d);
        $new_date->add(new DateInterval('P' . $increment));
        $CI = & get_instance();

        return $new_date->format($CI->mdl_settings->setting('date_format'));
    }

    /**
     * @originalName increment_date
     *
     * @originalFile date_helper.php
     */
    public static function incrementDate($date, string $increment): string
    {
        if ($date == null) {
            return '';
        }
        $new_date = new DateTime($date);
        $new_date->add(new DateInterval('P' . $increment));

        return $new_date->format('Y-m-d');
    }
}
