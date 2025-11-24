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

        // Get branches the user has access to
        $accessibleBranches = $user->branches()->pluck('branch.id')->toArray();

        $query = OvertimeRequest::with(['clock', 'department', 'approver'])
            ->whereIn('branch_id', $accessibleBranches);

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
            $query->where('branch_id', $request->branch_id);
        }

        $requests = $query->orderBy('status', 'asc')
            ->orderBy('date', 'desc')
            ->get();

        // Get user's available branches for filter dropdown
        $branches = $user->branches()->get();

        return view('hr.dashboard', compact('requests', 'branches'));
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
