<?php

namespace Modules\Payments\Services;

use AllowDynamicProperties;
use App\Services\BaseService;

#[AllowDynamicProperties]
class PaymentLogService extends BaseService
{
    public $table = 'ip_merchant_responses';

    public $primary_key = 'ip_merchant_responses.merchant_response_id';

    /**
     * @originalName defaultSelect
     *
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
     *
     * @originalFile PaymentLog.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('ip_merchant_responses.merchant_response_id DESC');
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile PaymentLog.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_invoices', 'ip_invoices.invoice_id = ip_merchant_responses.invoice_id');
    }
}
