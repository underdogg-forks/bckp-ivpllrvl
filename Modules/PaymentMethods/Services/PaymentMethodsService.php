<?php

namespace Modules\PaymentMethods\Services;

use Illuminate\Support\Facades\DB;
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
     * Applies the default ordering to the query.
     *
     * Orders results by the payment method name column (`ip_payment_methods.payment_method_name`).
     */
    public function orderBy()
    {
        $this->db->orderBy('ip_payment_methods.payment_method_name');
    }

    /**
     * Provide validation rules for payment method fields.
     *
     * Each entry maps a field key to an array with keys 'field', 'label', and 'rules' used by the validator.
     *
     * @return array{
     *     payment_method_name: array{field: string, label: string, rules: string}
     * } Mapping of field keys to their validation configuration
     */
    public function validationRules()
    {
        return ['payment_method_name' => ['field' => 'payment_method_name', 'label' => trans('payment_method'), 'rules' => 'required']];
    }

    /**
     * Retrieve all payment methods.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Modules\PaymentMethods\Models\PaymentMethod[] collection of PaymentMethod models
     */
    public function getAll()
    {
        return \Modules\PaymentMethods\Models\PaymentMethod::query()->get();
    }
}
