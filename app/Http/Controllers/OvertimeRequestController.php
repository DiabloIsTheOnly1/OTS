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
        // Handle branch/dept from QR redirect
        if ($request->has('branch') && $request->has('dept')) {
            session([
                'ot_branch_id' => $request->branch,
                'ot_department_id' => $request->dept
            ]);
            return redirect()->route('overtime.index');
        }

        $branchId = session('ot_branch_id');
        $departmentId = session('ot_department_id');

        $branch = Branch::find($branchId);
        $department = Department::find($departmentId);

        $query = OvertimeRequest::with(['branch', 'department', 'clocks', 'staff'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId));

        if ($request->filled('name')) {
            $query->whereHas('staff', fn($q) => $q->where('staff_name', 'like', "%{$request->name}%"));
        }
        if ($request->filled('from'))
            $query->whereDate('date', '>=', $request->from);
        if ($request->filled('to'))
            $query->whereDate('date', '<=', $request->to);
        if ($request->filled('status'))
            $query->where('status', $request->status);

        $requests = $query->paginate(15);

        // ONLY ONE LOOP — THIS IS THE ONLY PLACE WE TOUCH THE DATA
        foreach ($requests as $req) {
            // Actual hours
            $totalSec = $req->clocks->sum('total_time_taken');
            $req->total_hm = sprintf('%02d:%02d', floor($totalSec / 3600), floor(($totalSec % 3600) / 60));

            // Clock display
            $req->clock_in_display = $req->clocks->min('clock_in')?->format('H:i') ?? '-';
            $req->clock_out_display = $req->clocks->max('clock_out')?->format('H:i') ?? '-';

            // REQUESTED HOURS — FROM YOUR DB total_hours (e.g. 4.5 → 04:30)
            $hours = floor($req->total_hours ?? 0);
            $minutes = round(($req->total_hours ?? 0 - $hours) * 60);
            $req->requested_hm = sprintf(
                '%02d:%02d',
                floor($req->total_hours ?? 0),
                round(($req->total_hours ?? 0 - floor($req->total_hours ?? 0)) * 60)
            );
        }

        return view('overtime.index', compact('requests', 'branch', 'department'));
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
            'staff_id' => 'required|exists:staff,id',
            'branch_id' => 'required|exists:branch,id',
            'department_id' => 'required|exists:departments,id',
            'date' => 'required|date',
            'reg_no' => 'nullable|string|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type_of_work' => 'nullable|string',
        ]);

        // CALCULATE total_hours BEFORE modifying the array
        $start = \Carbon\Carbon::createFromFormat('H:i', $validated['start_time']);
        $end = \Carbon\Carbon::createFromFormat('H:i', $validated['end_time']);
        if ($end->lessThan($start))
            $end->addDay();

        $totalHours = round($start->diffInMinutes($end) / 60, 2);

        // NOW modify time format for DB
        $validated['start_time'] .= ':00';
        $validated['end_time'] .= ':00';

        // ADD total_hours to the CORRECT way
        $validated['total_hours'] = $totalHours;

        // Save session
        session([
            'ot_branch_id' => $validated['branch_id'],
            'ot_department_id' => $validated['department_id'],
        ]);

        $overtime = OvertimeRequest::create($validated);

        return redirect()->route('overtime.success', $overtime->id)
            ->with('submitted', true);
    }

    public function edit($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

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

 public function update(Request $request, OvertimeRequest $overtime)
{
    // Prevent edit if already clocked in
    if ($overtime->clocks()->exists()) {
        return back()->with('error', 'Cannot edit: Staff has already clocked in!');
    }

    // Accept ALL fields — no strict validation needed
    $data = $request->only([
        'staff_id',
        'date',
        'start_time',
        'end_time',
        'reg_no',
        'type_of_work',
        'remarks'
    ]);

    // Only recalculate total_hours if times are present
    if ($request->filled('start_time') && $request->filled('end_time')) {
        $start = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
        $end   = \Carbon\Carbon::createFromFormat('H:i', $request->end_time);
        if ($end->lessThan($start)) $end->addDay();

        $data['total_hours'] = round($start->diffInMinutes($end) / 60, 2);
    }

    $overtime->update($data);

    return back()->with('success', 'Overtime request updated successfully!');
}
    public function details($id)
    {
        $overtime = OvertimeRequest::with('clocks')->findOrFail($id);

        session([
            'ot_branch_id' => $overtime->branch_id,
            'ot_department_id' => $overtime->department_id,
        ]);

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

        session([
            'ot_branch_id' => $overtime->branch_id,
            'ot_department_id' => $overtime->department_id,
        ]);


        $qrUrl = url('/overtime/' . $overtime->id . '/details');

        return view('overtime.success', compact('overtime', 'qrUrl'));
    }

    public function show(OvertimeRequest $overtime)
    {
        $overtime->load('staff', 'branch', 'department', 'clocks', 'approver');
        return view('hr.overtime.view', compact('hr.overtime.view'));
    }

}


