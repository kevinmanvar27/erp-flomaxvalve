<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StakeHolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'user_type',
        'business_name',
        'address',
        'city',
        'state',
        'state_code',
        'GSTIN',
        'bank_name',
        'bank_account_no',
        'ifsc_code',
    ];
}
