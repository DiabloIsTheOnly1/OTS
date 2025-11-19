<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OvertimeRequest;

class OvertimeRequestController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_name' => 'required|string',
            'department' => 'nullable|string',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'reason' => 'nullable|string',
        ]);

        // Calculate total hours
        $start = strtotime($data['start_time']);
        $end = strtotime($data['end_time']);
        $data['total_hours'] = round(($end - $start) / 3600, 2);

        OvertimeRequest::create($data);

        return back()->with('success', 'Overtime request submitted successfully.');
    }
}
