<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'address',
        'create_date',
        'due_date',
        'invoice',
        'note',
        'lrno',
        'status',
        'sub_total',
        'received_amount',
        'discount',
        'balance',
        'customer_id',
        'cgst',
        'sgst',
        'igst',
        'discount_type',
        'pfcouriercharge',
        'courier_charge',
        'transport',
        'orderno',
        'round_off',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
    
    public function customer()
    {
        return $this->belongsTo(StakeHolder::class, 'customer_id', 'id');
    }

    /**
     * Get the pending amount (balance - received_amount) - balance includes GST
     */
    public function getPendingAmountAttribute()
    {
        return $this->balance - ($this->received_amount ?? 0);
    }

    /**
     * Check if payment is fully received
     */
    public function getIsFullyPaidAttribute()
    {
        return $this->pending_amount <= 0;
    }
}
