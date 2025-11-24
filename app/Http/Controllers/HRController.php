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

        // Branch and department access lists
        $accessibleBranches = $user->branches()->pluck('branch.id')->toArray();
        $accessibleDepartments = $user->department()->pluck('departments.id')->toArray();

        // Base query: filter by user's branch + department
        $query = OvertimeRequest::with(['clock', 'department', 'approver'])
            ->whereIn('branch_id', $accessibleBranches)
            ->whereIn('department_id', $accessibleDepartments);

        // Filter by employee name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by selected branch
        if ($request->filled('branch_id')) {
            // ensure selected branch is allowed
            if (in_array($request->branch_id, $accessibleBranches)) {
                $query->where('branch_id', $request->branch_id);
            }
        }

        // Filter by selected department
        if ($request->filled('department_id')) {
            if (in_array($request->department_id, $accessibleDepartments)) {
                $query->where('department_id', $request->department_id);
            }
        }

        $requests = $query->orderBy('status', 'asc')
            ->orderBy('date', 'desc')
            ->get();

        // For dropdown filters
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
}
