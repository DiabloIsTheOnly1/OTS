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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OvertimeExport;
use Barryvdh\DomPDF\Facade\Pdf;

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
            'requested_hours' => 'required|numeric|min:0.25|max:12', 
            'type_of_work' => 'nullable|string',
        ]);

        $data = $validated;
        $data['total_hours'] = $validated['requested_hours']; // this is the decimal hours
        unset($data['requested_hours']); // remove temp field

        // Save session for return view
        session([
            'ot_branch_id'       => $data['branch_id'],
            'ot_department_id'   => $data['department_id'],
        ]);

        $overtime = OvertimeRequest::create($data);

        return redirect()->route('overtime.success', $overtime->id)
            ->with('submitted', true);
    }

    public function edit($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        $user = Auth::user();
        $userBranchIds = $user->branches()->pluck('branch.id')->toArray();

        if ($user->access_all_departments) {
            $departmentIds = Department::pluck('id')->toArray();
        } else {
            $departmentIds = [$user->department_id];
        }

        $branches = Branch::whereIn('id', $userBranchIds)->get();
        $departments = Department::whereIn('id', $departmentIds)->get();
        $staffs = Staff::whereIn('branch_id', $userBranchIds)
            ->whereIn('department_id', $departmentIds)
            ->get();

        // Pass total_hours as requested_hours for the form
        $overtime->requested_hours = $overtime->total_hours;

        return view('overtime.form', compact(
            'overtime',
            'branches',
            'departments',
            'staffs'
        ));
    }

 public function update(Request $request, OvertimeRequest $overtime)
{
    if ($overtime->clocks()->exists()) {
        return back()->with('error', 'Cannot edit: Staff has already clocked in!');
    }

    $validated = $request->validate([
        'staff_id'     => 'required|exists:staff,id',
        'date'         => 'required|date',
        'total_hours'  => 'required|numeric|min:0.25|max:24', // ← NEW: only total_hours
        'reg_no'       => 'nullable|string|max:50',
        'type_of_work' => 'nullable|string',
        'remarks'      => 'nullable|string',
    ]);

    $overtime->update($request->only([
        'staff_id',
        'date',
        'total_hours',
        'reg_no',
        'type_of_work',
        'remarks',
    ]));

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

    private function formatRequestedHours($overtime)
    {
        if (!$overtime->total_hours || $overtime->total_hours <= 0) {
            return '-';
        }

        $hours = floor($overtime->total_hours);
        $minutes = round(($overtime->total_hours - $hours) * 60);

        return sprintf('%02d:%02d', $hours, $minutes);
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

    //For PDF Export and Excel Export

    public function exportExcel(Request $request){

    $query = $this->buildOvertimeQuery($request);
    $overtimes = $query->get();

    return Excel::download(new OvertimeExport($overtimes), 'overtime_requests_' . now()->format('Y-m-d') . '.xlsx');
}

public function exportPdf(Request $request)
{
    $query = $this->buildOvertimeQuery($request);
    $overtimes = $query->get();

    // Same formatting loop
    foreach ($overtimes as $req) {
        $totalSec = $req->clocks->sum('total_time_taken');
        $req->total_hm = $totalSec > 0 
            ? sprintf('%02d:%02d', floor($totalSec / 3600), floor(($totalSec % 3600) / 60))
            : '-';

        $hours = floor($req->total_hours ?? 0);
        $minutes = round(($req->total_hours ?? 0 - $hours) * 60);
        $req->requested_hm = $req->total_hours > 0 
            ? sprintf('%02d:%02d', $hours, $minutes)
            : '-';
    }

    $pdf = Pdf::loadView('hr.pdf', compact('overtimes'))
        ->setPaper('a4', 'landscape');

    return $pdf->download('overtime_requests_' . now()->format('Y-m-d') . '.pdf');
}

// Example: Extracted filter logic (copy from your index method and adjust)
private function buildOvertimeQuery(Request $request)
{
    $query = OvertimeRequest::with(['staff', 'branch', 'department', 'clocks']);

    if ($request->branch_id) {
        $query->where('branch_id', $request->branch_id);
    }
    if ($request->department_id) {
        $query->where('department_id', $request->department_id);
    }
    if ($request->name) {
        $query->whereHas('staff', function ($q) use ($request) {
            $q->where('staff_name', 'like', '%' . $request->name . '%');
        });
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

    return $query->orderBy('date', 'desc'); // Add sorting as needed
}

public function preview(Request $request)
{
    $query = $this->buildOvertimeQuery($request);
    $overtimes = $query->get();

    // ADD THIS EXACT SAME LOOP FROM YOUR index() METHOD
    foreach ($overtimes as $req) {
        // Actual hours from clock sessions
        $totalSec = $req->clocks->sum('total_time_taken');
        $req->total_hm = $totalSec > 0 
            ? sprintf('%02d:%02d', floor($totalSec / 3600), floor(($totalSec % 3600) / 60))
            : '-';

        // Requested hours from total_hours field (decimal → HH:MM)
        $hours = floor($req->total_hours ?? 0);
        $minutes = round(($req->total_hours ?? 0 - $hours) * 60);
        $req->requested_hm = $req->total_hours > 0 
            ? sprintf('%02d:%02d', $hours, $minutes)
            : '-';
    }

    return view('hr.preview', compact('overtimes'));
}

public function clockDetails($id)
{
    $overtime = OvertimeRequest::with('staff', 'branch', 'department', 'clocks')->findOrFail($id);

    // Calculate remaining OT hours
    $totalHours = $overtime->total_hours ?? 0;
    $totalSec = $overtime->clocks->sum('total_time_taken');
    $clockedHours = $totalSec / 3600; // Convert seconds to hours
    $remainingHours = max(0, $totalHours - $clockedHours);

    return view('overtime.clock-details', compact('overtime', 'remainingHours'));
}


}


