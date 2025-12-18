<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalRejection extends Model
{
    use HasFactory;

    // Specify the table if it's not the pluralized version of the model name
    protected $table = 'internal_rejections';

    // Define the fillable fields
    protected $fillable = [
        'user_code',
        'parts',
        'qty',
        'reason',
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_code', 'usercode');
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }
}
