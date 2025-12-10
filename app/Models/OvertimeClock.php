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
        'auto_flag',
        'total_time_taken',
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'auto_flag' => 'boolean',
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

    public function autoCloseIfExceeded($totalHours)
    {
        // if already clocked out → do nothing
        if ($this->clock_out) {
            return false;
        }

        $requestedMinutes = (int) round($totalHours * 60);

        $expectedEnd = $this->clock_in->copy()->addMinutes($requestedMinutes);
        $graceEnd = $expectedEnd->copy()->addMinutes(30);

        // Check if user exceeded allowed limit
        if (now()->greaterThan($graceEnd)) {

            // Auto clock-out
            $this->clock_out = $graceEnd;
            $this->total_time_taken = $this->clock_in->diffInSeconds($graceEnd);
            $this->auto_flag = true;

            $this->save();

            return true;
        }

        return false;
    }

}