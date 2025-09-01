<?php

namespace App\Helpers;

class PaymentsHelper
{
    /**
     * @originalName get_currencies
     *
     * @originalFile payments_helper.php
     */
    public static function getCurrencies(): array
    {
        //retrieve the available currencies
        $currencies    = new ISOCurrencies();
        $ISOCurrencies = [];
        foreach ($currencies as $currency) {
            $ISOCurrencies[$currency->getCode()] = $currency->getCode();
        }

        return $ISOCurrencies;
    }
}
