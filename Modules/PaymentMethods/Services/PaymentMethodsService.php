<?php

namespace Modules\PaymentMethods\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;

#[AllowDynamicProperties]
class PaymentMethodsService extends BaseService
{
    public $table = 'ip_payment_methods';

    public $primary_key = 'ip_payment_methods.payment_method_id';

    /**
     * @originalName defaultSelect
     *
     * @originalFile PaymentMethod.php
     */
    public function defaultSelect()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    /**
     * @originalName orderBy
     *
     * @originalFile PaymentMethod.php
     */
    public function orderBy()
    {
        $this->db->orderBy('ip_payment_methods.payment_method_name');
    }

    /**
     * @originalName validationRules
     *
     * @originalFile PaymentMethod.php
     */
    public function validationRules()
    {
        return ['payment_method_name' => ['field' => 'payment_method_name', 'label' => trans('payment_method'), 'rules' => 'required']];
    }

    /**
     * Get all payment methods
     *
     * @return array Collection of payment methods
     */
    public function getAll()
    {
        return $this->get()->result();
    }
}
