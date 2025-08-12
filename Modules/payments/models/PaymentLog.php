<?php

namespace Modules\Payments\Models;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */
#[AllowDynamicProperties]
class PaymentLog extends ResponseModel
{
    public $table = 'ip_merchant_responses';
    public $primary_key = 'ip_merchant_responses.merchant_response_id';
    /**
     * @originalName defaultSelect
     * @originalFile PaymentLog.php
     */
    public function defaultSelect()
    {
        $this->db->select('
            SQL_CALC_FOUND_ROWS
            ip_invoices.invoice_number,
            ip_merchant_responses.*', false);
    }
    /**
     * @originalName defaultOrderBy
     * @originalFile PaymentLog.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('ip_merchant_responses.merchant_response_id DESC');
    }
    /**
     * @originalName defaultJoin
     * @originalFile PaymentLog.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_invoices', 'ip_invoices.invoice_id = ip_merchant_responses.invoice_id');
    }
}
