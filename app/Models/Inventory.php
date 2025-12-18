<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'create_date',
        'invoice_number',
        'supplier_id', 
        'quantity',
        'sku_code',
        'amount',
        'purchase_order_invoice',
    ];

    public function supplier()
    {
        return $this->belongsTo(StakeHolder::class, 'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(InventoryItem::class, 'inventory_id');
    }
}
