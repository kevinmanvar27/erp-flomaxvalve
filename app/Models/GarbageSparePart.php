<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarbageSparePart extends Model
{
    use HasFactory;

    protected $fillable = ['garbage_id', 'spare_part_id', 'type', 'size', 'weight', 'quantity'];

    public function garbage()
    {
        return $this->belongsTo(Garbage::class);
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }
}
