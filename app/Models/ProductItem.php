<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductItem extends Model
{
    use HasFactory;
    
    // Specify the correct table name
    protected $table = 'product_spare_part';
    
    protected $fillable = [
        'product_id', 'spare_part_id', 'quantity'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class, 'spare_part_id');
    }
}
