<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OvertimeRequest extends Model
{
    use HasFactory;

    protected $table = 'overtime_requests';

    protected $fillable = [
        'name',
        'position',
        'branch_id',
        'department_id',
        'date',
        'reason',
        'start_time',
        'end_time',
        'qr_code',
        'clocked_in_at',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'clocked_in_at' => 'datetime',
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

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

public function clock()
{
    return $this->hasOne(\App\Models\OvertimeClock::class);
}

}
