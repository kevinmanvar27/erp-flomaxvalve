<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobWorkChallanItem extends Model
{
    use HasFactory;

    protected $fillable = ['job_work_challans_id', 'spare_part_id', 'quantity', 'remaining_quantity', 'wt_pc', 'material_specification', 'remark'];

    public function jobWorkChallan()
    {
        return $this->belongsTo(JobWorkChallan::class, 'job_work_challans_id');
    }

    // Relationship with SparePart
    public function sparePart()
    {
        return $this->belongsTo(SparePart::class, 'spare_part_id');
    }
}
