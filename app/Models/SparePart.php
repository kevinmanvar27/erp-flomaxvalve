<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparePart extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
        'size',
        'weight',
        'unit',
        'qty',
        'rate',
        'minimum_qty'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_spare_part');
    }

    public function garbages()
    {
        return $this->belongsToMany(Garbage::class, 'garbage_spare_parts')
                    ->withPivot('quantity') // Include quantity in the pivot table
                    ->withTimestamps();
    }

    public function jobWorkChallanItems()
    {
        return $this->hasMany(JobWorkChallanItem::class, 'spare_part_id');
    }
}
