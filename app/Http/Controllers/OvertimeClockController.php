<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OvertimeRequest;
use App\Models\OvertimeClock;

class OvertimeClockController extends Controller
{
    public function clock($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        // Get existing clock record or create new
        $clock = OvertimeClock::where('overtime_request_id', $overtime->id)->first();

        if (!$clock) {
            // Clock In
            $clock = OvertimeClock::create([
                'overtime_request_id' => $overtime->id,
                'clock_in' => now(),
            ]);

            $message = 'Clocked In';
            $scannedAt = $clock->clock_in;
        } elseif (!$clock->clock_out) {
            // Clock Out
            $clock->clock_out = now();
            $clock->total_time_taken = $clock->clock_in->diffInSeconds($clock->clock_out);
            $clock->save();

            $message = 'Clocked Out';
            $scannedAt = $clock->clock_out;
        } else {
            // Already clocked out
            $message = 'You have already clocked out';
            $scannedAt = $clock->clock_out;
        }

        return view('overtime.clock_success', [
            'overtime' => $overtime,
            'clock' => $clock,
            'message' => $message,
            'scannedAt' => $scannedAt,
        ]);
    }
}
