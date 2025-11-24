<?php

namespace App\Http\Controllers;

use App\Models\OvertimeClock;
use App\Models\OvertimeRequest;
use Carbon\Carbon;

class OvertimeClockController extends Controller
{

    public function clockIn($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        // Check existing clock record
        $clock = OvertimeClock::where('overtime_request_id', $id)->first();

        // Already clocked in?
        if ($clock && $clock->clock_in) {
            return back()->with('error', 'You already clocked in.');
        }

        // Create or update clock record
        if (!$clock) {
            OvertimeClock::create([
                'overtime_request_id' => $id,
                'clock_in' => Carbon::now(),
            ]);
        } else {
            $clock->update([
                'clock_in' => Carbon::now(),
            ]);
        }

        return back()->with('success', 'Clock-in successful.');
    }

       public function clockOut($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        $clock = OvertimeClock::where('overtime_request_id', $id)->first();

        if (!$clock || !$clock->clock_in) {
            return back()->with('error', 'You must clock in first.');
        }

        if ($clock->clock_out) {
            return back()->with('error', 'You already clocked out.');
        }

        $clockOut = Carbon::now();
        // Ensure clock_in is a Carbon instance (in case it's stored as string)
        $clockIn = $clock->clock_in instanceof Carbon ? $clock->clock_in : Carbon::parse($clock->clock_in);

        // Compute seconds and ensure non-negative integer (defensive)
        // $seconds = (int) max(0, $clockOut->diffInSeconds($clockIn));
        $seconds = $clockIn->diffInSeconds($clockIn);

        $clock->update([
            'clock_out' => $clockOut,
            'total_time_taken' => $seconds,
        ]);

        return back()->with('success', 'Clock-out successful.');
    }
}
