<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OvertimeRequest extends Model
{
    use HasFactory;

    protected $table = 'overtime_requests';

    protected $fillable = [
        'staff_id',
        'branch_id',
        'department_id',
        'date',
        'start_time',
        'end_time',
        'type_of_work',
        'reg_no',
        'status',
        'total_hours',
        'approved_by',
        'approved_at',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // Relationships
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

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

    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function clocks()
    {
        return $this->hasMany(OvertimeClock::class);
    }

        public function clockSessions()
{
    return $this->hasMany(OvertimeClock::class, 'overtime_request_id');
}
}

