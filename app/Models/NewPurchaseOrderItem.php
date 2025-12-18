<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewPurchaseOrderItem extends Model
{
    use HasFactory;

    protected $table = 'new_purchase_order_item';

    protected $fillable = [
        'new_purchase_order_id', 
        'spare_part_id', 
        'quantity', 
        'price', 
        'amount', 
        'remark',
        'product_unit',
        'material_specification', // New column
        'unit',                   // New column
        'rate_kgs',               // New column
        'per_pc_weight',          // New column
        'total_weight',           // New column
        'delivery_date'           // New column
    ];

    public function newPurchaseOrder()
    {
        return $this->belongsTo(NewPurchaseOrder::class, 'new_purchase_order_id');
    }



    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }
}
