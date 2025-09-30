<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantResponse extends Model
{
    public $timestamps = false;

    protected $table = 'ip_merchant_responses';

    protected $fillable = [
        'invoice_id',
        'merchant_response_successful',
        'merchant_response_date',
        'merchant_response_driver',
        'merchant_response',
        'merchant_response_reference',
    ];
}
