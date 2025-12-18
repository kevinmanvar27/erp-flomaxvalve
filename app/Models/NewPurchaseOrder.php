<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewPurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'new_purchase_order';

    protected $fillable = [
        'address',
        'create_date',
        'due_date',
        'invoice',
        'note',
        'status',
        'sub_total',
        'discount',
        'balance',
        'customer_id',
        'cgst',
        'sgst',
        'discount_type',
        'prno',
        'po_revision_and_date',
        'reason_of_revision',
        'quotation_ref_no',
        'remarks',
        'pr_date',
    ];

    public function items()
    {
        return $this->hasMany(NewPurchaseOrderItem::class, 'new_purchase_order_id');
    }


    public function customer()
    {
        return $this->belongsTo(StakeHolder::class, 'customer_id', 'id');
    }
}
