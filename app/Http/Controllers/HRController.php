<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OvertimeRequest;
use App\Models\User;

class HRController extends Controller
{
    /**
     * Display the HR dashboard with all overtime requests.
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $accessibleBranches = $user->branches()->pluck('branch.id')->toArray();
        $accessibleDepartments = $user->department()->pluck('departments.id')->toArray();

        $query = OvertimeRequest::with(['clocks', 'department', 'approver', 'rejector'])
            ->whereIn('branch_id', $accessibleBranches)
            ->whereIn('department_id', $accessibleDepartments);

        // Filters
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
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

        $requests = $query->orderBy('status', 'asc')
            ->orderBy('date', 'desc')
            ->get();

        // ---- Compute total time for all sessions ----
        foreach ($requests as $r) {
            $totalSeconds = $r->clocks->sum('total_time_taken');

            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);

            $r->total_hm = sprintf('%02d:%02d', $hours, $minutes);
        }

        // Dropdowns
        $branches = $user->branches()->get();
        $departments = $user->department()->get();

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

}
