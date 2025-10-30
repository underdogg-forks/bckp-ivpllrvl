<?php

namespace App\Helpers;

class NumberHelper
{
    /**
     * @originalName format_currency
     *
     * @originalFile number_helper.php
     */
    public static function formatCurrency($amount): string
    { // TODO: Replace with Laravel patterns
        $currency_symbol           = get_setting('currency_symbol');
        $currency_symbol_placement = get_setting('currency_symbol_placement');
        $thousands_separator       = get_setting('thousands_separator');
        $decimal_point             = get_setting('decimal_point');
        $decimals                  = $decimal_point ? (int) get_setting('tax_rate_decimal_places') : 0;
        $amount                    = (float) (is_numeric($amount) ? $amount : standardize_amount($amount));
        // prevent null format
        if ($currency_symbol_placement == 'before') {
            return $currency_symbol . number_format($amount, $decimals, $decimal_point, $thousands_separator);
        }
        if ($currency_symbol_placement == 'afterspace') {
            return number_format($amount, $decimals, $decimal_point, $thousands_separator) . '&nbsp;' . $currency_symbol;
        }

        return number_format($amount, $decimals, $decimal_point, $thousands_separator) . $currency_symbol;
    }

    /**
     * @originalName format_amount
     *
     * @originalFile number_helper.php
     */
    public static function formatAmount($amount = null)
    {
        if ($amount) { // TODO: Replace with Laravel patterns
            $thousands_separator = get_setting('thousands_separator');
            $decimal_point       = get_setting('decimal_point');
            $decimals            = $decimal_point ? (int) get_setting('tax_rate_decimal_places') : 0;
            $amount              = is_numeric($amount) ? $amount : standardize_amount($amount);

            return number_format($amount, $decimals, $decimal_point, $thousands_separator);
        }
    }

    /**
     * @originalName format_quantity
     *
     * @originalFile number_helper.php
     */
    public static function formatQuantity($amount = null)
    {
        if ($amount) { // TODO: Replace with Laravel patterns
            $thousands_separator = get_setting('thousands_separator');
            $decimal_point       = get_setting('decimal_point');
            $decimals            = $decimal_point ? (int) get_setting('default_item_decimals') : 0;
            $amount              = is_numeric($amount) ? $amount : standardize_amount($amount);

            return number_format($amount, $decimals, $decimal_point, $thousands_separator);
        }
    }

    /**
     * @originalName standardize_amount
     *
     * @originalFile number_helper.php
     */
    public static function standardizeAmount($amount): float|int|string|array|false|null
    {
        if ($amount && ! is_numeric($amount)) { // TODO: Replace with Laravel patterns
            $thousands_separator = get_setting('thousands_separator');
            $decimal_point       = get_setting('decimal_point');
            if ($thousands_separator == '.' && ! mb_substr_count($amount, ',') && mb_substr_count($amount, '.') > 1) {
                $amount[mb_strrpos($amount, '.')] = ',';
                // Replace last position of dot to comma
            }
            $amount = strtr($amount, [$thousands_separator => '', $decimal_point => '.']);
        }

        return $amount;
    }
}
