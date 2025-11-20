<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OvertimeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'branch_id',
        'department_id',
        'date',
        'work_description',
        'status',
    ];

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
