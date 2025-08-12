<?php

namespace App\Helpers;

class CountryHelper
{
    /**
     * @originalName get_country_list
     *
     * @originalFile country_helper.php
     */
    public static function getCountryList(string $cldr)
    {
        if (file_exists(APPPATH . 'helpers/country-list/' . $cldr . '/country.php')) {
            return include APPPATH . 'helpers/country-list/' . $cldr . '/country.php';
        }

        return include APPPATH . 'helpers/country-list/en/country.php';
    }

    /**
     * @originalName get_country_name
     *
     * @originalFile country_helper.php
     */
    public static function getCountryName($cldr, $countrycode)
    {
        $countries = get_country_list($cldr);

        return $countries[$countrycode] ?? $countrycode;
    }
}
