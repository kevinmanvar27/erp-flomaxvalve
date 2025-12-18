<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'problem', 'status'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
