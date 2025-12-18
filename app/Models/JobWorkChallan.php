<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobWorkChallan extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_work_name', 
        'pdf_files', 
        'user_id', 
        'po_revision_and_date', 
        'reason_of_revision', 
        'quotation_ref_no', 
        'remarks', 
        'pr_date', 
        'prno', 
        'po_no',
        'customer_id'
    ];

    protected $casts = [
        'pdf_files' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }

    public function items()
    {
        return $this->hasMany(JobWorkChallanItem::class, 'job_work_challans_id');
    }

    public function customer()
    {
        return $this->belongsTo(StakeHolder::class, 'customer_id', 'id');
    }
}
