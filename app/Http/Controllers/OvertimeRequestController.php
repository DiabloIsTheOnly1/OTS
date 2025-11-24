<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OvertimeRequest;
use App\Models\OvertimeClock;
use App\Models\Branch;
use App\Models\Department;


class OvertimeRequestController extends Controller
{
    public function selectPage()
    {
        $branches = Branch::all(); // but filter only branches user has access to
        $departments = Department::all();

        return view('overtime.select', compact('branches', 'departments'));
    }

    public function setFilters(Request $request)
    {
        session([
            'ot_branch_id' => $request->branch_id,
            'ot_department_id' => $request->department_id,
        ]);

        return redirect()->route('overtime.index');
    }

    public function index(Request $request)
    {
        // Default filters from session
        $branchId = session('ot_branch_id');
        $departmentId = session('ot_department_id');

        // $branches = Branch::whereIn('id', auth()->user()->branches->pluck('id'))->get();
        // $departments = Department::all();

        $query = OvertimeRequest::with(['branch', 'department', 'clock'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId));
        // --- Filters ---
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->name) {
            $query->where('name', 'like', "%{$request->name}%");
        }

        if ($request->from) {
            $query->whereDate('date', '>=', $request->from);
        }

        if ($request->to) {
            $query->whereDate('date', '<=', $request->to);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(15);

        return view('overtime.index', [
            'requests' => $requests,
            'branches' => Branch::all(),
            'departments' => Department::all(),
        ]);
    }

    // Show OT request form
    public function create()
    {
        $branches = Branch::all();
        $departments = Department::all();

        return view('overtime.form', compact('branches', 'departments'));
    }

    // Store OT request
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'branch_id' => 'nullable|exists:branch,id',
            'department_id' => 'nullable|exists:departments,id',
            'date' => 'nullable|date',
            'reason' => 'nullable|string',
        ]);

        $overtime = OvertimeRequest::create($request->all());

        $qrUrl = url('/overtime/' . $overtime->id . '/details');

        return view('overtime.success', compact('overtime', 'qrUrl'));

    }

    public function details($id)
    {
        $overtime = OvertimeRequest::with('clock')->findOrFail($id);

        return view('overtime.details', compact('overtime'));
    }

// Clock-in via QR
public function clockin($id)
{
    $overtime = OvertimeRequest::findOrFail($id);

    $clock = OvertimeClock::firstOrCreate([
        'overtime_request_id' => $overtime->id
    ]);

    // Only clock in if not already done
    if (!$clock->clock_in) {
        $clock->clock_in = now();
        $clock->save();
        $message = 'Clocked In';
        $scannedAt = $clock->clock_in;
    } else {
        $message = 'Already Clocked In';
        $scannedAt = $clock->clock_in;
    }

    return view('overtime.clock_success', compact('overtime', 'clock', 'message', 'scannedAt'));
}

    public function clockOut($id)
{
    $overtime = OvertimeRequest::findOrFail($id);

    // Get the clock entry
    $clock = OvertimeClock::firstOrCreate([
        'overtime_request_id' => $overtime->id
    ]);

    // Only clock out if not already done
    if (!$clock->clock_out) {
        $clock->clock_out = now();

        // Calculate total time in seconds if clock_in exists
        if ($clock->clock_in && $clock->clock_out) {
            // Ensure Carbon instances (handle string values returned from DB)
            $clockOut = $clock->clock_out instanceof \Carbon\Carbon ? $clock->clock_out : \Carbon\Carbon::parse($clock->clock_out);
            $clockIn = $clock->clock_in instanceof \Carbon\Carbon ? $clock->clock_in : \Carbon\Carbon::parse($clock->clock_in);

            // Compute absolute non-negative difference
            $seconds = $clockIn->diffInSeconds($clockOut);
            $clock->total_time_taken = $seconds;
        }

        $clock->save();
        $message = 'Clocked Out';
        $scannedAt = $clock->clock_out;
    } else {
        $message = 'You have already clocked out';
        $scannedAt = $clock->clock_out;
    }

    return view('overtime.clock_success', compact('overtime', 'clock', 'message', 'scannedAt'));
}


    public function qr($id)
    {
        $overtime = OvertimeRequest::with(['branch', 'department'])->findOrFail($id);
        $qrUrl = url('/overtime/' . $overtime->id . '/details');

        return view('overtime.qr', compact('overtime', 'qrUrl'));
    }

}
