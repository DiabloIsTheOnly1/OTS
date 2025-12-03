<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OvertimeRequest;
use App\Models\OvertimeClock;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

        $request->validate([
            'branch_id' => 'required',
            'department_id' => 'required',
        ]);

        session([
            'ot_branch_id' => $request->branch_id,
            'ot_department_id' => $request->department_id,
        ]);

        return redirect()->route('overtime.index');
    }

    public function index(Request $request)
    {
        $branch = Branch::find(session('ot_branch_id'));
        $department = Department::find(session('ot_department_id'));

        // Default filters
        $branchId = session('ot_branch_id');
        $departmentId = session('ot_department_id');

        // Load request + all clock sessions
        $query = OvertimeRequest::with(['branch', 'department', 'clocks'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId));

        // --- Filters ---
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filters (GET)
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

        /**
         * Add total hours to each request
         */
        foreach ($requests as $req) {
            // --- Total H:M ---
            $totalSec = $req->clocks->sum('total_time_taken');
            $hours = floor($totalSec / 3600);
            $minutes = floor(($totalSec % 3600) / 60);
            $req->total_hm = sprintf('%02d:%02d', $hours, $minutes);

            // --- Clock In (earliest) ---
            $firstClockIn = $req->clocks->min('clock_in');
            $req->clock_in_display = $firstClockIn
                ? date('H:i', strtotime($firstClockIn))
                : '-';

            // --- Clock Out (latest) ---
            $lastClockOut = $req->clocks->max('clock_out');
            $req->clock_out_display = $lastClockOut
                ? date('H:i', strtotime($lastClockOut))
                : '-';
        }

        return view('overtime.index', [
            'requests' => $requests,
            'branches' => Branch::all(),
            'departments' => Department::all(),
            'branch' => $branch,
            'department' => $department,
        ]);
    }


    // Show OT request form
    public function create()
    {
        $user = Auth::user();

        // 1. Get user's branches
        $userBranchIds = $user->branches()->pluck('branch.id')->toArray();

        // 2. Determine which departments user can access
        if ($user->access_all_departments) {
            // User can access all departments
            $departmentIds = Department::pluck('id')->toArray();
            $departments = Department::all();
        } else {
            // User only belongs to ONE department
            $departmentIds = [$user->department_id];
            $departments = Department::whereIn('id', $departmentIds)->get();
        }

        // 3. Branch list = only user's branches
        $branches = Branch::whereIn('id', $userBranchIds)->get();

        // 4. Filter staff by user's branch + allowed department(s)
        $staffs = Staff::whereIn('branch_id', $userBranchIds)
            ->whereIn('department_id', $departmentIds)
            ->get();

        // Empty model for create mode
        $overtime = new OvertimeRequest();

        return view('overtime.form', compact(
            'overtime',
            'branches',
            'departments',
            'staffs'
        ));
    }

        // Store OT request
        public function store(Request $request)
        {
            $validated = $request->validate([
                'staff_id'      => 'required|exists:staff,id',
                'branch_id'     => 'required|exists:branch,id',
                'department_id' => 'required|exists:departments,id',
                'date'          => 'required|date',
                'reg_no'        => 'nullable|string|max:100',
                'start_time'    => 'required|date_format:H:i',
                'end_time'      => 'required|date_format:H:i|after:start_time',
                'type_of_work'  => 'nullable|string',
            ]);

            // Fix the time format: Laravel saves TIME as H:i:s → make sure it's H:i:00
            $validated['start_time'] = $validated['start_time'] . ':00';
            $validated['end_time']   = $validated['end_time'] . ':00';

            // Calculate total hours correctly (including overnight)
            $start = \Carbon\Carbon::createFromFormat('H:i:s', $validated['start_time']);
            $end   = \Carbon\Carbon::createFromFormat('H:i:s', $validated['end_time']);

            // Handle overnight shift
            if ($end->lessThan($start)) {
                $end->addDay();
            }

            $totalMinutes = $start->diffInMinutes($end);
            $validated['total_hours'] = round($totalMinutes / 60, 2); // e.g., 4.5, 8.25

            // Save it
            $overtime = OvertimeRequest::create($validated);

            $qrUrl = url('/overtime/' . $overtime->id . '/details');

            return view('overtime.success', compact('overtime', 'qrUrl'));
        }

    public function edit($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        // Check if any clock in exists
        // $hasClockedIn = $overtime->clocks()->exists();

        // if ($hasClockedIn) {
        //     return redirect()
        //         ->route('overtime.index')
        //         ->with('error', 'You cannot edit this request because clock-in has already started.');
        // }

        $branches = Branch::all();
        $departments = Department::all();

        $selectedBranch = $overtime->branch_id;
        $selectedDepartment = $overtime->department_id;

        return view('overtime.form', compact(
            'overtime',
            'branches',
            'departments',
            'selectedBranch',
            'selectedDepartment'
        ));
    }

    public function update(Request $request, $id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        // Prevent update
        if ($overtime->clocks()->exists()) {
            return redirect()
                ->route('overtime.index')
                ->with('error', 'Cannot update: user has already clocked in.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'branch_id' => 'required|exists:branch,id',
            'department_id' => 'required|exists:departments,id',
            'date' => 'required|date',
            'work_done' => 'required|string',
            'reason' => 'required|string',
        ]);

        $overtime->update($validated);

        return redirect()
            ->route('overtime.index')
            ->with('success', 'Overtime request updated successfully!');
    }

    public function details($id)
    {
        $overtime = OvertimeRequest::with('clocks')->findOrFail($id);

        return view('overtime.details', compact('overtime'));
    }

    // Clock-in via QR
    public function clockin($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        // Check if there is an unfinished clock entry
        $lastClock = OvertimeClock::where('overtime_request_id', $overtime->id)
            ->whereNull('clock_out')
            ->latest()
            ->first();

        if ($lastClock) {
            return back()->with('error', 'Please clock out before clocking in again.');
        }

        // Create new clock entry
        $clock = OvertimeClock::create([
            'overtime_request_id' => $overtime->id,
            'clock_in' => now(),
        ]);

        return view('overtime.clock_success', [
            'overtime' => $overtime,
            'clock' => $clock,
            'message' => 'Clocked In',
            'scannedAt' => $clock->clock_in,
        ]);
    }


    public function clockOut($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        // Get the latest incomplete clock entry
        $clock = OvertimeClock::where('overtime_request_id', $overtime->id)
            ->whereNull('clock_out')
            ->latest()
            ->first();

        if (!$clock) {
            return back()->with('error', 'No active clock-in found.');
        }

        $clock->clock_out = now();

        // Calculate total seconds for this session
        $start = $clock->clock_in instanceof \Carbon\Carbon
            ? $clock->clock_in
            : \Carbon\Carbon::parse($clock->clock_in);

        $end = $clock->clock_out instanceof \Carbon\Carbon
            ? $clock->clock_out
            : \Carbon\Carbon::parse($clock->clock_out);

        $clock->total_time_taken = $start->diffInSeconds($end);

        $clock->save();

        return view('overtime.clock_success', [
            'overtime' => $overtime,
            'clock' => $clock,
            'message' => 'Clocked Out',
            'scannedAt' => $clock->clock_out,
        ]);
    }


    public function qr($id)
    {
        $overtime = OvertimeRequest::with(['branch', 'department'])->findOrFail($id);
        $qrUrl = url('/overtime/' . $overtime->id . '/details');

        return view('overtime.success', compact('overtime', 'qrUrl'));
    }


}


