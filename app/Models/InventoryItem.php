<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'quantity',
        'sku_code',
        'spare_part_id',
        'inventory_id',
    ];

    // Define relationships if needed
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class, 'spare_part_id');
    }
}
