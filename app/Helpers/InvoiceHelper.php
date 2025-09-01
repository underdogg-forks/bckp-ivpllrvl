<?php

namespace App\Helpers;

class InvoiceHelper
{
    /**
     * @originalName invoice_logo
     *
     * @originalFile invoice_helper.php
     */
    public static function invoiceLogo(): string
    {
        $CI = & get_instance();
        if ($CI->mdl_settings->setting('invoice_logo')) {
            return '<img src="' . base_url() . 'uploads/' . $CI->mdl_settings->setting('invoice_logo') . '">';
        }

        return '';
    }

    /**
     * @originalName invoice_logo_pdf
     *
     * @originalFile invoice_helper.php
     */
    public static function invoiceLogoPdf(): string
    {
        $CI = & get_instance();
        if ($CI->mdl_settings->setting('invoice_logo')) {
            $absolutePath = dirname(dirname(__DIR__));

            return '<img src="' . $absolutePath . '/uploads/' . $CI->mdl_settings->setting('invoice_logo') . '" id="invoice-logo">';
        }

        return '';
    }

    /**
     * @originalName invoice_genCodeline
     *
     * @originalFile invoice_helper.php
     */
    public static function invoiceGenCodeline(string $slipType, $amount, $rnumb, $subNumb): string
    {
        $isEur = false;
        if ((int) $slipType > 14) {
            $isEur = true;
        } else {
            $amount = 0.5 * round((float) $amount / 0.5, 1);
        }
        if ( ! $isEur && $amount > 99999999.95) {
            throw new Error('Invalid amount');
        }
        if ($isEur && $amount > 99999999.98999999) {
            throw new Error('Invalid amount');
        }
        $amountLine    = sprintf('%010d', $amount * 100);
        $checkSlAmount = invoice_recMod10($slipType . $amountLine);
        if ( ! preg_match('/\\d{2}-\\d{1,6}-\\d{1}/', $subNumb)) {
            throw new Error('Invalid subscriber number');
        }
        $subNumb = explode('-', $subNumb);
        $fullSub = $subNumb[0] . sprintf('%06d', $subNumb[1]) . $subNumb[2];
        $rnumb   = preg_replace('/\s+/', '', $rnumb);

        return $slipType . $amountLine . $checkSlAmount . '>' . $rnumb . '+ ' . $fullSub . '>';
    }

    /**
     * @originalName invoice_recMod10
     *
     * @originalFile invoice_helper.php
     */
    public static function invoiceRecMod10($in): int
    {
        $line  = [0, 9, 4, 6, 8, 2, 7, 1, 3, 5];
        $carry = 0;
        $chars = mb_str_split($in);
        foreach ($chars as $char) {
            $carry = $line[($carry + (int) $char) % 10];
        }

        return (10 - $carry) % 10;
    }

    /**
     * @originalName invoice_qrcode
     *
     * @originalFile invoice_helper.php
     */
    public static function invoiceQrcode($invoice_id): string
    {
        $CI = & get_instance();
        if ($CI->mdl_settings->setting('qr_code') && $CI->mdl_settings->setting('qr_code_iban') && $CI->mdl_settings->setting('qr_code_bic')) {
            $invoice = $CI->mdl_invoices->get_by_id($invoice_id);
            if ((float) $invoice->invoice_balance) {
                $CI->load->library('Modules\Core\Libraries\QrCode', ['invoice' => $invoice]);
                $qrcode_data_uri = $CI->qrcode->generate();

                return '<img src="' . $qrcode_data_uri . '" alt="QR Code" id="invoice-qr-code">';
            }
        }

        return '';
    }
}
