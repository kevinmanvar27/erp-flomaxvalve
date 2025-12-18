<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'valve_type',
        'product_code',
        'actuation',
        'pressure_rating',
        'valve_size',
        'valve_size_rate',
        'media',
        'flow',
        'sku_code',
        'mrp',
        'media_temperature',
        'media_temperature_rate',
        'body_material',
        'hsn_code',
        'primary_material_of_construction'
    ];

    public function spareParts()
    {
        return $this->belongsToMany(SparePart::class, 'product_spare_part');
    }

    public function items()
    {
        return $this->hasMany(ProductItem::class);
    }

    public function supports()
    {
        return $this->hasMany(Support::class);
    }
}
