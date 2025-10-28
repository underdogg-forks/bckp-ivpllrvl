<?php

namespace Modules\Payments\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\Payments\Models\PaymentLog;

#[AllowDynamicProperties]
class PaymentLogsService extends BaseService
{
    public $table = 'ip_merchant_responses';

    public $primary_key = 'ip_merchant_responses.merchant_response_id';

    /**
     * Get a base PaymentLog query with relationships for select.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultSelect(): \Illuminate\Database\Eloquent\Builder
    {
        return PaymentLog::query()->with('invoice');
    }

    /**
     * Get a PaymentLog query ordered by id descending.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultOrderBy(): \Illuminate\Database\Eloquent\Builder
    {
        return PaymentLog::query()->orderByDesc('merchant_response_id');
    }

    /**
     * Get a PaymentLog query with relationships (joins).
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultJoin(): \Illuminate\Database\Eloquent\Builder
    {
        return PaymentLog::query()->with('invoice');
    }
}
