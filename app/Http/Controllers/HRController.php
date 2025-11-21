<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\OvertimeRequest;

class HRController extends Controller
{
    /**
     * Display the HR dashboard with all overtime requests.
     */
    public function index(Request $request)
    {
        $query = OvertimeRequest::query();

        // Filter by employee name (db column is `name`)
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('status', 'asc')
                          ->orderBy('date', 'desc')
                          ->get();

        return view('hr.dashboard', compact('requests'));
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
