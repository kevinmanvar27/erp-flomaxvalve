<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo',
        'address',
        'company_name',
        'email',
        'mobile_number',
        'state',
        'city',
        'pin_code',
        'company_gstin',
        'authorized_signatory',
        'purchase_order_logo',
        'prepared_by',
        'approved_by',
        'purchase_order_gstin',
        'purchase_order_mobile_number	',
        'purchase_order_email',
        'purchase_order_address',
    ];
}
