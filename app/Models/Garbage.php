<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garbage extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'type', 'quantity'];

    public function spareParts()
    {
        return $this->belongsToMany(SparePart::class, 'garbage_spare_parts')
                    ->withPivot('quantity') // Include quantity in the pivot table
                    ->withTimestamps();
    }
}
