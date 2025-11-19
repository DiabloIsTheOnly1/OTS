<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class OvertimeRequest extends Model
{
    protected $fillable = [
        'employee_name',
        'department',
        'date',
        'start_time',
        'end_time',
        'total_hours',
        'reason',
        'status',
        'approved_by',
        'approved_at',
    ];

    // Relationship to User (HR who approved)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
