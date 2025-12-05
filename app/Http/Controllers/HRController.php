<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OvertimeRequest;
use App\Models\User;
use App\Models\Department;

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

            // -------------------------------
            // Fetch results
            // -------------------------------
            $requests = $query->orderBy('status', 'asc')
                            ->orderBy('date', 'desc')
                            ->get();

            // -------------------------------
            // Compute Actual & Requested Hours
            // -------------------------------
            foreach ($requests as $r) {
                // Actual hours (from clock sessions)
                $totalSeconds = $r->clocks->sum('total_time_taken');
                $hours = floor($totalSeconds / 3600);
                $minutes = floor(($totalSeconds % 3600) / 60);
                $r->total_hm = sprintf('%02d:%02d', $hours, $minutes);

                // REQUESTED HOURS — FROM total_hours in DB (this was missing!)
                $reqHours   = floor($r->total_hours ?? 0);
                $reqMinutes = round(($r->total_hours - $reqHours) * 60);
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

    /**
     * Approve an overtime request.
     */
    public function approve(int $id)
    {
        $overtimeRequest = OvertimeRequest::findOrFail($id);
        $overtimeRequest->status = 'approved';
        $overtimeRequest->approved_by = Auth::id(); // Intelephense-safe
        $overtimeRequest->approved_at = now();
        $overtimeRequest->save();

        return redirect()->back()->with('success', 'Overtime request approved.');
    }

    /**
     * Reject an overtime request.
     */
    public function reject(int $id)
    {
        $overtimeRequest = OvertimeRequest::findOrFail($id);
        $overtimeRequest->status = 'rejected';
        $overtimeRequest->approved_by = Auth::id(); // Intelephense-safe
        $overtimeRequest->approved_at = now();
        $overtimeRequest->save();

        return redirect()->back()->with('success', 'Overtime request rejected.');
    }

    public function updateRemarks(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'nullable|string|max:255',
        ]);

        $ot = OvertimeRequest::findOrFail($id);

        // Only allow editing while pending
        // if ($ot->status !== 'pending') {
        //     return back()->with('error', 'Cannot update remarks after approval/rejection.');
        // }

        $ot->remarks = $request->remarks;
        $ot->save();

        return back()->with('success', 'Remarks updated.');
    }

    public function viewForm($id)
    {
           $overtime = OvertimeRequest::with('branch','department','clocks')->findOrFail($id);

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
