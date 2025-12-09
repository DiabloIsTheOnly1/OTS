<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OvertimeClock extends Model
{
    use HasFactory;

    protected $fillable = [
        'overtime_request_id',
        'clock_in',
        'clock_out',
        'total_time_taken',
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    public function overtimeRequest()
    {
        return $this->belongsTo(OvertimeRequest::class);
    }

    public function getTotalHmAttribute()
    {
        if (!$this->total_time_taken)
            return null;

        // Ensure we operate on a non-negative integer
        $seconds = (int) abs($this->total_time_taken);
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        return sprintf('%dh %dm', $hours, $minutes);
    }



}