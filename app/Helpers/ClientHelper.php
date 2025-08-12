<?php

namespace App\Helpers;

class ClientHelper
{
    /**
     * @originalName format_client
     *
     * @originalFile client_helper.php
     */
    public static function formatClient($client, $show_title = true): string
    {
        // GetController an id
        if ($client && is_numeric($client)) {
            $CI = & get_instance();
            if ( ! property_exists($CI, 'mdl_clients')) {
                $CI->load->model('clients/mdl_clients');
            }
            $client = $CI->mdl_clients->get_by_id($client);
        }
        // Not exist or find, Stop.
        if (empty($client->client_name)) {
            return '';
        }
        $client_title = '';
        if ($show_title && ! empty($client->client_title)) {
            $client_title = ucfirst(in_array($client->client_title, ClientTitleEnum::VALUES, true) ? trans($client->client_title) : $client->client_title) . ' ';
        }

        return $client_title . $client->client_name . (empty($client->client_surname) ? '' : ' ' . $client->client_surname);
    }

    /**
     * @originalName format_gender
     *
     * @originalFile client_helper.php
     */
    public static function formatGender($gender)
    {
        if ($gender == 0) {
            return trans('gender_male');
        }
        if ($gender == 1) {
            return trans('gender_female');
        }

        return trans('gender_other');
    }
}
