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
        $perPage = $request->input('per_page', 15);

        // -------------------------------
        // Branch access
        // -------------------------------
        $accessibleBranches = $user->branches()->pluck('branch.id')->toArray();

        // -------------------------------
        // Department access (UPDATED)
        // -------------------------------
        if ($user->access_all_departments) {
            $accessibleDepartments = Department::pluck('id')->toArray();
        } else {
            $accessibleDepartments = $user->departments()
                ->pluck('departments.id')
                ->toArray();
        }

        // -------------------------------
        // Auto-close active clocks
        // -------------------------------
        $activeClocks = OvertimeClock::whereNull('clock_out')->get();

        foreach ($activeClocks as $clock) {
            $clock->autoCloseIfExceeded($clock->overtimeRequest->total_hours);
        }

        // -------------------------------
        // Base query
        // -------------------------------
        $baseQuery = OvertimeRequest::with([
            'staff',
            'branch',
            'department',
            'clocks',
            'approver',
            'rejector',
        ])
            ->whereIn('branch_id', $accessibleBranches)
            ->whereIn('department_id', $accessibleDepartments);

        // -------------------------------
        // Filters
        // -------------------------------
        if ($request->filled('status')) {
            $baseQuery->where('status', $request->status);
        }

        if (
            $request->filled('branch_id') &&
            in_array($request->branch_id, $accessibleBranches)
        ) {
            $baseQuery->where('branch_id', $request->branch_id);
        }

        if (
            $request->filled('department_id') &&
            in_array($request->department_id, $accessibleDepartments)
        ) {
            $baseQuery->where('department_id', $request->department_id);
        }

        if ($request->filled('month')) {

            [$year, $month] = explode('-', $request->month);

            $baseQuery->whereYear('date', $year)
                ->whereMonth('date', $month);

        } else {

            if ($request->filled('from')) {
                $baseQuery->whereDate('date', '>=', $request->from);
            }

            if ($request->filled('to')) {
                $baseQuery->whereDate('date', '<=', $request->to);
            }

        }
        // -------------------------------
        // Request List
        // -------------------------------
        $requests = (clone $baseQuery)
            ->orderBy('status')
            ->orderByDesc('date')
            ->paginate($perPage)
            ->withQueryString();

        foreach ($requests as $r) {

            // Actual OT
            $seconds = $r->clocks->sum('total_time_taken');
            $minutes = floor($seconds / 60);

            $r->actual_minutes = $minutes;
            $r->actual_hm = sprintf(
                '%02d:%02d',
                floor($minutes / 60),
                $minutes % 60
            );

            // Requested OT
            $requestedMinutes = floor(($r->total_hours ?? 0) * 60);

            $r->requested_minutes = $requestedMinutes;
            $r->requested_hm = sprintf(
                '%02d:%02d',
                floor($requestedMinutes / 60),
                $requestedMinutes % 60
            );
        }

        // -------------------------------
        // OT Summary
        // -------------------------------
        $otSummary = (clone $baseQuery)
            ->with(['staff', 'clocks'])
            ->orderBy('staff_id')
            ->orderBy('date')
            ->get()
            ->groupBy('staff_id')
            ->map(function ($rows) {

                $totalMinutes = 0;

                $rows->each(function ($r) use (&$totalMinutes) {

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
                    ),
                ];
            });

        // -------------------------------
        // Dropdowns
        // -------------------------------
        $branches = $user->branches()->get();

        $departments = $user->access_all_departments
            ? Department::orderBy('department_name')->get()
            : $user->departments()->orderBy('department_name')->get();

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

        $canHod = $user->canAccess('hod_approval');
        $canHq = $user->canAccess('hq_approval');

        $firstClockIn = $r->clocks()
            ->orderBy('clock_in', 'asc')
            ->value('clock_in');

        if (!$firstClockIn && !$canHq) {
            return back()->with('error', 'No clock-in record found.');
        }

        if ($firstClockIn) {
            $hoursSinceFirstClockIn = $firstClockIn->diffInHours(now());

            if ($hoursSinceFirstClockIn <= 48 && !$canHod) {
                return back()->with('error', 'Only HOD can approve within the first 48 hours.');
            }

            if ($hoursSinceFirstClockIn > 48 && !$canHq) {
                return back()->with('error', 'Only HQ can approve after 48 hours.');
            }
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

        $canHod = $user->canAccess('hod_approval');
        $canHq = $user->canAccess('hq_approval');

        $firstClockIn = $r->clocks()
            ->orderBy('clock_in', 'asc')
            ->value('clock_in');

        if (!$firstClockIn && !$canHq) {
            return back()->with('error', 'No clock-in record found.');
        }

        if ($firstClockIn) {
            $hoursSinceFirstClockIn = $firstClockIn->diffInHours(now());

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

        }
        // Actual minutes
        $actualMinutes = $firstClockIn
            ? intval($r->clocks->sum('total_time_taken') / 60)
            : PHP_INT_MAX; // HQ can approve any amount

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

        $canHod = $user->canAccess('hod_approval');
        $canHq = $user->canAccess('hq_approval');

        $firstClockIn = $overtimeRequest->clocks()
            ->orderBy('clock_in', 'asc')
            ->value('clock_in');

        if (!$firstClockIn && !$canHq) {
            return back()->with('error', 'No clock-in record found.');
        }

        if ($firstClockIn) {
            $hoursSinceFirstClockIn = $firstClockIn->diffInHours(now());

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
        }

        $employee = $overtimeRequest->staff->staff_name;

        $overtimeRequest->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Rejected overtime request for ' . $employee . '.');
    }

    public function revertPending($id)
    {
        $user = auth()->user();

        if (!$user->canAccess('hq_approval')) {
            abort(403, 'Unauthorized');
        }

        $request = OvertimeRequest::findOrFail($id);

        // Optional safety: only allow revert if already approved/rejected
        if (!in_array($request->status, ['approved', 'rejected'])) {
            return back()->with('error', 'Only approved or rejected requests can be reverted.');
        }

        $employee = $request->staff->staff_name;
        $request->update([
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'approved_hours' => null,
        ]);

        return back()->with('success', 'Overtime request for ' . $employee . ' reverted to pending.');
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
