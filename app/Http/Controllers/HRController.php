<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OvertimeRequest;
use App\Models\User;
use App\Models\Department;
use Carbon\Carbon;
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
        // Base query
        // -------------------------------
        $query = OvertimeRequest::with(['staff', 'branch', 'department', 'clocks', 'approver', 'rejector'])
            ->whereIn('branch_id', $accessibleBranches)
            ->whereIn('department_id', $accessibleDepartments);

        // -------------------------------
        // Filters
        // -------------------------------
        if ($request->filled('name')) {
            $query->whereHas('staff', function ($q) use ($request) {
                $q->where('staff_name', 'like', '%' . $request->name . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id') && in_array($request->branch_id, $accessibleBranches)) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('department_id') && in_array($request->department_id, $accessibleDepartments)) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->to);
        }

        if ($request->filled('month')) {
    
            $month = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            $query->whereBetween('date', [$startOfMonth, $endOfMonth]);
        }



        // -------------------------------
        // Fetch results
        // -------------------------------
        $requests = $query->orderBy('status', 'asc')
            ->orderBy('date', 'desc')
            ->get();

        // -------------------------------
        // Compute Actual & Requested Hours (NO DECIMALS)
        // -------------------------------
        foreach ($requests as $r) {

            // -------------------------------
            // ACTUAL HOURS — from clock sessions
            // -------------------------------
            $totalSeconds = $r->clocks->sum('total_time_taken');
            $totalMinutes = floor($totalSeconds / 60);
            $r->actual_minutes = $totalMinutes;

            // Convert to HH:MM
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
            $r->actual_hm = sprintf('%02d:%02d', $hours, $minutes);


            // -------------------------------
            // REQUESTED HOURS — from DB
            // -------------------------------
            $requestedDecimal = $r->total_hours ?? 0; // Example: 1.5

            // Convert decimal → minutes
            $requestedMinutes = floor($requestedDecimal * 60);
            $r->requested_minutes = $requestedMinutes;

            // Convert minutes → HH:MM
            $reqHours = floor($requestedMinutes / 60);
            $reqMinutes = $requestedMinutes % 60;
            $r->requested_hm = sprintf('%02d:%02d', $reqHours, $reqMinutes);
        }

        // -------------------------------
        // Dropdown lists
        // -------------------------------
        $branches = $user->branches()->get();

        $departments = $user->access_all_departments
            ? Department::all()
            : Department::where('id', $user->department_id)->get();

        return view('hr.dashboard', compact('requests', 'branches', 'departments'));
    }


    public function approveFull($id)
    {
        $r = OvertimeRequest::findOrFail($id);

        $user = Auth::user();
        $createdAt = $r->created_at;
        $hoursSinceCreated = $createdAt->diffInHours(now());

        $canHod = $user->canAccess('hod_approval');
        $canHq = $user->canAccess('hq_approval');

        if ($hoursSinceCreated <= 48) {
            if (!$canHod) {
                return back()->with('error', 'Only HOD can approve within the first 48 hours.')->send();
            }
        }

        if ($hoursSinceCreated > 48) {
            if (!$canHq) {
                return back()->with('error', 'Only HQ approvers can approve after 48 hours.')->send();
            }
        }

        // Calculate actual minutes
        $actualMinutes = intval($r->clocks->sum('total_time_taken') / 60);

        $r->update([
            // 'approved_hours' => $actualMinutes / 60,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'status' => 'approved'
        ]);

        return back()->with('success', 'Full overtime approved.');
    }

    public function approvePartial(Request $request, $id)
    {
        $r = OvertimeRequest::findOrFail($id);

        $user = Auth::user();
        $createdAt = $r->created_at;
        $hoursSinceCreated = $createdAt->diffInHours(now());

        $canHod = $user->canAccess('hod_approval');
        $canHq = $user->canAccess('hq_approval');

        if ($hoursSinceCreated <= 48) {
            if (!$canHod) {
                return back()->with('error', 'Only HOD can approve within the first 48 hours.')->send();
            }
        }

        if ($hoursSinceCreated > 48) {
            if (!$canHq) {
                return back()->with('error', 'Only HQ approvers can approve after 48 hours.')->send();
            }
        }

        // Actual minutes
        $actualMinutes = intval($r->clocks->sum('total_time_taken') / 60);

        // Requested to approve
        $approved = intval($request->approved_minutes);

        if ($approved > $actualMinutes) {
            return back()->with('error', 'Approved minutes cannot exceed actual worked minutes.');
        }

        $r->update([
            'approved_hours' => $approved / 60,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'status' => 'approved'
        ]);

        return back()->with('success', 'Partial overtime approved.');
    }


    /**
     * Reject an overtime request.
     */
    public function reject(int $id)
    {
        $overtimeRequest = OvertimeRequest::findOrFail($id);
        $user = Auth::user();
        $createdAt = $overtimeRequest->created_at;
        $hoursSinceCreated = $createdAt->diffInHours(now()); // FIXED

        $canHod = $user->canAccess('hod_approval');
        $canHq = $user->canAccess('hq_approval');

        // HOD window (0–48 hours)
        if ($hoursSinceCreated <= 48) {
            if (!$canHod) {
                return back()->with('error', 'Only HOD can approve within the first 48 hour.');
            }
        }

        if ($hoursSinceCreated > 48) {
            if (!$canHq) {
                return back()->with('error', 'Only HQ approvers can approve after 48 hour.');
            }
        }

        $overtimeRequest->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Overtime request rejected.');
    }

    public function updateRemarks(Request $request, $id)
    {
        $request->validate(['remarks' => 'nullable|string']);

        $overtime = OvertimeRequest::findOrFail($id);
        $overtime->remarks = $request->remarks;
        $overtime->save();

        return back()->with('success', 'Remarks updated.');
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
