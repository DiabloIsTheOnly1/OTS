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

    // Search name
    if ($request->filled('name')) {
        $query->whereHas('staff', fn($q) => 
            $q->where('staff_name', 'like', "%{$request->name}%")
        );
    }

    // Date range filters
    if ($request->filled('from'))
        $query->whereDate('date', '>=', $request->from);

    if ($request->filled('to'))
        $query->whereDate('date', '<=', $request->to);

    //  MONTH FILTER
    if ($request->filled('month')) {
        $query->whereMonth('date', $request->month);
    }

    if ($request->filled('year')) {
        $query->whereYear('date', $request->year);
    }

    // Status filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Pagination
    $requests = $query->paginate(15);

    // COMPUTE TOTALS + TIME FORMATTING
    foreach ($requests as $req) {
        $totalSec = $req->clocks->sum('total_time_taken');

        $hours = floor($totalSec / 3600);
        $minutes = floor(($totalSec % 3600) / 60);

        $req->actual_hm = $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
        $req->total_hm = $req->actual_hm;

        // Clock display
        $req->clock_in_display = $req->clocks->min('clock_in')?->format('H:i') ?? '-';
        $req->clock_out_display = $req->clocks->max('clock_out')?->format('H:i') ?? '-';

        // Requested hours
        $hoursReq = floor($req->total_hours ?? 0);
        $minutesReq = round(($req->total_hours ?? 0 - $hoursReq) * 60);
        $req->requested_hm = sprintf('%02d:%02d', $hoursReq, $minutesReq);
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
            'requested_hours_h' => 'required|integer|min:0|max:12',
            'requested_hours_m' => 'required|integer|min:0|max:59',
            'type_of_work' => 'nullable|string',
        ]);


        // Combine hours & minutes safely
        $hours = $validated['requested_hours_h'] ?? 0;
        $minutes = $validated['requested_hours_m'] ?? 0;
        $totalHours = $hours + ($minutes / 60);


        $overtime = OvertimeRequest::create([
            'staff_id' => $validated['staff_id'],
            'branch_id' => $validated['branch_id'],
            'department_id' => $validated['department_id'],
            'date' => $validated['date'],
            'reg_no' => $validated['reg_no'] ?? null,
            'type_of_work' => $validated['type_of_work'] ?? null,
            'total_hours' => $totalHours,
        ]);

        // Save session for branch/dept
        session([
            'ot_branch_id' => $validated['branch_id'],
            'ot_department_id' => $validated['department_id'],
        ]);

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
            'staff_id' => 'required|exists:staff,id',
            'date' => 'required|date',
            'requested_hours_h' => 'required|integer|min:0|max:24',
            'requested_hours_m' => 'required|integer|min:0|max:59',
            'reg_no' => 'nullable|string|max:50',
            'type_of_work' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        // Combine hours & minutes
        $hours = $validated['requested_hours_h'] ?? 0;
        $minutes = $validated['requested_hours_m'] ?? 0;
        $totalHours = $hours + ($minutes / 60);

        $overtime->update([
            'staff_id' => $validated['staff_id'],
            'date' => $validated['date'],
            'total_hours' => $totalHours,
            'reg_no' => $validated['reg_no'] ?? null,
            'type_of_work' => $validated['type_of_work'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
        ]);

        return back()->with('success', 'Overtime request updated successfully!');
    }

    public function details($id)
    {
        $overtime = OvertimeRequest::with('clocks')->findOrFail($id);

        $activeClocks = OvertimeClock::whereNull('clock_out')->get();

        foreach ($activeClocks as $clock) {
            $clock->autoCloseIfExceeded($clock->overtimeRequest->total_hours);
        }

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

        // --- 1. Prevent extra clock-in if already reached requested hours ---

        // Sum all completed clocks for this overtime request
        $totalSeconds = OvertimeClock::where('overtime_request_id', $overtime->id)
            ->whereNotNull('clock_out')
            ->sum('total_time_taken');

        // Requested time in seconds
        $requestedSeconds = (int) round($overtime->total_hours * 3600);

        if ($totalSeconds >= $requestedSeconds) {
            return back()->with('error', 'You have already completed the requested overtime hours. No more clock-ins allowed.');
        }

        // --- 2. Prevent clock-in if there is an active session ---
        $lastClock = OvertimeClock::where('overtime_request_id', $overtime->id)
            ->whereNull('clock_out')
            ->latest()
            ->first();

        if ($lastClock) {
            return back()->with('error', 'Please clock out before clocking in again.');
        }

        // --- 3. Create new clock entry ---
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

        $clock = OvertimeClock::where('overtime_request_id', $id)
            ->whereNull('clock_out')
            ->latest()
            ->first();

        // First try to auto-close if exceeded
        if ($clock && $clock->autoCloseIfExceeded($overtime->total_hours)) {
            return back()->with('info', 'System has already auto clocked out this session.');
        }

        // After auto-close, no active entry remains
        if (!$clock) {
            return back()->with('error', 'No active clock-in found.');
        }

        // Prevent manual clock-out if already auto clocked out
        if ($clock->auto_flag) {
            return back()->with('error', 'You were already auto clocked out by the system.');
        }

        // Perform normal manual clock-out
        $clock->clock_out = now();
        $clock->total_time_taken = $clock->clock_in->diffInSeconds($clock->clock_out);
        $clock->auto_flag = false;
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

        return view('hr.overtime.view', compact('overtime'));
    }

    //For PDF Export and Excel Export

    public function exportExcel(Request $request)
    {
        $query = $this->buildOvertimeQuery($request);

    return Excel::download(new OvertimeExport($query), 'Overtime_requests_' . now()->format('Y-m-d') . '.xlsx');
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
            $minutes = round((($req->total_hours ?? 0) - $hours) * 60);

            $req->requested_hm = sprintf('%02d:%02d', $hours, $minutes);
        }

        $pdf = Pdf::loadView('hr.pdf', compact('overtimes'))
            ->setPaper('a4', 'landscape');

    return $pdf->download('Overtime_requests_' . now()->format('Y-m-d') . '.pdf');
}

 public function exportMonthlyExcel(Request $request)
{
    $month = $request->month;

    if (!$month) {
        return back()->with('error', 'Please select a month first.');
    }

    $data = OvertimeRequest::with(['staff', 'branch', 'department'])
                ->whereMonth('date', $month)
                ->get();

    return Excel::download(new OvertimeExport($data), "Overtime_Month_$month.xlsx");
}


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

    if ($request->month) {
        [$year, $month] = explode('-', $request->month);
        $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    if ($request->year) {
        $query->whereYear('date', $request->year);
    }

    if ($request->status) {
        $query->where('status', $request->status);
    }

    return $query->orderBy('date', 'desc'); 
}

    public function preview(Request $request)
    {
        $query = $this->buildOvertimeQuery($request);
        $overtimes = $query->get();

    
    foreach ($overtimes as $req) {

        
        $totalSec = $req->clocks->sum('total_time_taken');
        $req->total_hm = $totalSec > 0 
            ? sprintf('%02d:%02d', floor($totalSec / 3600), floor(($totalSec % 3600) / 60))
            : '-';

        
        $hours = floor($req->total_hours ?? 0);
        $minutes = round((($req->total_hours ?? 0) - $hours) * 60);

            $req->requested_hm = sprintf('%02d:%02d', $hours, $minutes);
        }

        return view('hr.preview', compact('overtimes'));
    }


    public function clockDetails($id)
    {
        $overtime = OvertimeRequest::with('staff', 'branch', 'department', 'clocks')->findOrFail($id);

   
    $totalHours = $overtime->total_hours ?? 0;
    $totalSec = $overtime->clocks->sum('total_time_taken');
    $clockedHours = $totalSec / 3600; 
    $remainingHours = max(0, $totalHours - $clockedHours);

        return view('overtime.clock-details', compact('overtime', 'remainingHours'));
    }


}


