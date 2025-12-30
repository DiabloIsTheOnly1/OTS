<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OvertimeRequest;
use App\Models\User;
use App\Models\Department;
use App\Models\OvertimeClock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OvertimeExport;

class HRController extends Controller
{
    /**
     * Display the HR dashboard with all overtime requests.
     */

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $perPage = request('per_page', 15);

        // -------------------------------
        // Branch access
        // -------------------------------
        $accessibleBranches = $user->branches()->pluck('branch.id')->toArray();

        // -------------------------------
        // Department access
        // -------------------------------
        if ($user->access_all_departments) {
            $accessibleDepartments = Department::pluck('id')->toArray();
        } else {
            $accessibleDepartments = [$user->department_id];
        }

        // -------------------------------
        // Auto-close active clocks
        // -------------------------------
        $activeClocks = OvertimeClock::whereNull('clock_out')->get();
        foreach ($activeClocks as $clock) {
            $clock->autoCloseIfExceeded($clock->overtimeRequest->total_hours);
        }

        // --------------------------------------------------
// MONTH CONTEXT (DEFAULT = CURRENT MONTH)
// --------------------------------------------------
        $month = $request->filled('month')
            ? Carbon::createFromFormat('Y-m', $request->month)
            : now();

        // --------------------------------------------------
// Base query
// --------------------------------------------------
        $baseQuery = OvertimeRequest::with([
            'staff',
            'branch',
            'department',
            'clocks',
            'approver',
            'rejector'
        ])
            ->whereIn('branch_id', $accessibleBranches)
            ->whereIn('department_id', $accessibleDepartments)
            ->whereBetween('date', [
                $month->copy()->startOfMonth(),
                $month->copy()->endOfMonth()
            ]);


        if ($request->filled('status')) {
            $baseQuery->where('status', $request->status);
        }

        if ($request->filled('branch_id') && in_array($request->branch_id, $accessibleBranches)) {
            $baseQuery->where('branch_id', $request->branch_id);
        }

        if ($request->filled('department_id') && in_array($request->department_id, $accessibleDepartments)) {
            $baseQuery->where('department_id', $request->department_id);
        }

        if ($request->filled('from')) {
            $baseQuery->whereDate('date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $baseQuery->whereDate('date', '<=', $request->to);
        }

        // if ($request->filled('month')) {
        //     $month = Carbon::createFromFormat('Y-m', $request->month);
        //     $baseQuery->whereBetween('date', [
        //         $month->startOfMonth(),
        //         $month->endOfMonth()
        //     ]);
        // }

        // =====================================================
        // 1️⃣ REQUEST LIST (PAGINATED)
        // =====================================================
        $requests = (clone $baseQuery)
            ->orderBy('status', 'asc')
            ->orderBy('date', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // -------------------------------
        // Compute hours (existing logic)
        // -------------------------------
        foreach ($requests as $r) {

            // ACTUAL
            $totalSeconds = $r->clocks->sum('total_time_taken');
            $totalMinutes = floor($totalSeconds / 60);
            $r->actual_minutes = $totalMinutes;

            $r->actual_hm = sprintf(
                '%02d:%02d',
                floor($totalMinutes / 60),
                $totalMinutes % 60
            );

            // REQUESTED
            $requestedMinutes = floor(($r->total_hours ?? 0) * 60);
            $r->requested_minutes = $requestedMinutes;

            $r->requested_hm = sprintf(
                '%02d:%02d',
                floor($requestedMinutes / 60),
                $requestedMinutes % 60
            );
        }

        // --------------------------------------------------
        // OT SUMMARY – GROUPED BY STAFF
        // --------------------------------------------------
        $otSummary = (clone $baseQuery)
            ->whereBetween('date', [
                $month->copy()->startOfMonth(),
                $month->copy()->endOfMonth()
            ])
            ->with(['staff', 'clocks'])
            ->orderBy('staff_id')
            ->orderBy('date')
            ->get()
            ->groupBy('staff_id')
            ->map(function ($rows) {

                $totalMinutes = 0;

                $rows->each(function ($r) use (&$totalMinutes) {
                    // actual minutes already computed via clocks
                    $seconds = $r->clocks->sum('total_time_taken');
                    $minutes = floor($seconds / 60);
                    $r->actual_minutes = $minutes;

                    $r->actual_hm = sprintf(
                        '%02d:%02d',
                        floor($minutes / 60),
                        $minutes % 60
                    );

                    $totalMinutes += $minutes;
                });

                return [
                    'staff' => $rows->first()->staff,
                    'rows' => $rows,
                    'total_hm' => sprintf(
                        '%02d:%02d',
                        floor($totalMinutes / 60),
                        $totalMinutes % 60
                    )
                ];
            });

        // -------------------------------
        // Dropdowns
        // -------------------------------
        $branches = $user->branches()->get();

        $departments = $user->access_all_departments
            ? Department::all()
            : Department::where('id', $user->department_id)->get();

        return view('hr.dashboard', compact(
            'requests',
            'otSummary',
            'branches',
            'departments'
        ));
    }


    public function approveFull($id)
    {
        $r = OvertimeRequest::findOrFail($id);
        $user = Auth::user();

        $firstClockIn = $r->clocks()
            ->orderBy('clock_in', 'asc')
            ->value('clock_in');

        if (!$firstClockIn) {
            return back()->with('error', 'No clock-in record found.');
        }

        $hoursSinceFirstClockIn = $firstClockIn->diffInHours(now());

        $canHod = $user->canAccess('hod_approval');
        $canHq = $user->canAccess('hq_approval');

        if ($hoursSinceFirstClockIn <= 48 && !$canHod) {
            return back()->with('error', 'Only HOD can approve within the first 48 hours.');
        }

        if ($hoursSinceFirstClockIn > 48 && !$canHq) {
            return back()->with('error', 'Only HQ can approve after 48 hours.');
        }

        $actualMinutes = intval($r->clocks->sum('total_time_taken') / 60);
        $employee = $r->staff->staff_name;

        $r->update([
            'approved_by' => $user->id,
            'approved_at' => now(),
            'status' => 'approved',
        ]);

        return back()->with('success', 'Approved overtime for ' . $employee . '.');
    }


    public function approvePartial(Request $request, $id)
    {
        $r = OvertimeRequest::findOrFail($id);

        $user = Auth::user();
        $firstClockIn = $r->clocks()
            ->orderBy('clock_in', 'asc')
            ->value('clock_in');

        if (!$firstClockIn) {
            return back()->with('error', 'No clock-in record found.');
        }

        $hoursSinceFirstClockIn = $firstClockIn->diffInHours(now());

        $canHod = $user->canAccess('hod_approval');
        $canHq = $user->canAccess('hq_approval');

        if ($hoursSinceFirstClockIn <= 48) {
            if (!$canHod) {
                return back()->with('error', 'Only HOD can approve within the first 48 hours.')->send();
            }
        }

        if ($hoursSinceFirstClockIn > 48) {
            if (!$canHq) {
                return back()->with('error', 'Only HQ can approve after 48 hours.')->send();
            }
        }

        // Actual minutes
        $actualMinutes = intval($r->clocks->sum('total_time_taken') / 60);

        // Requested to approve
        $approved = intval($request->approved_minutes);

        if ($approved > $actualMinutes) {
            return back()->with('error', 'Approved minutes cannot exceed actual worked minutes.');
        }

        $employee = $r->staff->staff_name;

        $r->update([
            'approved_hours' => $approved / 60,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'status' => 'approved'
        ]);

        return back()->with('success', 'Approved overtime hour for ' . $employee . '.');
    }


    /**
     * Reject an overtime request.
     */
    public function reject(int $id)
    {
        $overtimeRequest = OvertimeRequest::findOrFail($id);
        $user = Auth::user();
        $firstClockIn = $overtimeRequest->clocks()
            ->orderBy('clock_in', 'asc')
            ->value('clock_in');

        if (!$firstClockIn) {
            return back()->with('error', 'No clock-in record found.');
        }

        $hoursSinceFirstClockIn = $firstClockIn->diffInHours(now());


        $canHod = $user->canAccess('hod_approval');
        $canHq = $user->canAccess('hq_approval');

        // HOD window (0–48 hours)
        if ($hoursSinceFirstClockIn <= 48) {
            if (!$canHod) {
                return back()->with('error', 'Only HOD can approve within the first 48 hour.');
            }
        }

        if ($hoursSinceFirstClockIn > 48) {
            if (!$canHq) {
                return back()->with('error', 'Only HQ can reject after 48 hour.');
            }
        }

        $employee = $overtimeRequest->staff->staff_name;

        $overtimeRequest->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Rejected overtime request for ' . $employee . '.');
    }

    public function updateRemarks(Request $request, $id)
    {
        $request->validate(['remarks' => 'nullable|string']);

        $overtime = OvertimeRequest::findOrFail($id);
        $staffName = $overtime->staff->staff_name;
        $overtime->remarks = $request->remarks;
        $overtime->save();

        return back()->with('success', 'Remarks for ' . $staffName . ' updated to ' . $overtime->remarks . '.');
    }

    public function viewForm($id)
    {
        $overtime = OvertimeRequest::with('branch', 'department', 'clocks')->findOrFail($id);

        // Compute total time for each clock session
        foreach ($overtime->clocks as $c) {
            $seconds = $c->total_time_taken;
            $h = floor($seconds / 3600);
            $m = floor(($seconds % 3600) / 60);
            $c->total_hm = sprintf('%02d:%02d', $h, $m);
        }

        // Compute total OT hours
        $totalSeconds = $overtime->clocks->sum('total_time_taken');
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $overtime->total_hm = sprintf('%02d:%02d', $hours, $minutes);

        return view('hr.overtime_form_view', compact('overtime'));
    }




}
