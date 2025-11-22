<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OvertimeRequest;
use App\Models\Branch;
use App\Models\Department;

class OvertimeRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = OvertimeRequest::with(['branch', 'department'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        // --- Filters ---
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->name) {
            $query->where('name', 'like', "%{$request->name}%");
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

        $requests = $query->paginate(15);

        return view('overtime.index', [
            'requests' => $requests,
            'branches' => Branch::all(),
            'departments' => Department::all(),
        ]);
    }

    // Show OT request form
    public function create()
    {
        $branches = Branch::all();
        $departments = Department::all();

        return view('overtime.form', compact('branches', 'departments'));
    }

    // Store OT request
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'branch_id' => 'nullable|exists:branch,id',
            'department_id' => 'nullable|exists:departments,id',
            'date' => 'nullable|date',
            'reason' => 'nullable|string',
        ]);

        $overtime = OvertimeRequest::create($validated);

        return view('overtime.success', [
            'overtime' => $overtime,
        ]);
    }

    // Clock-in via QR
    public function clockin($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        if ($overtime->clocked_in_at) {
            return response()->json([
                'status' => 'error',
                'message' => 'Already clocked in at ' . $overtime->clocked_in_at,
            ]);
        }

        $overtime->update(['clocked_in_at' => now()]);

        return response()->json([
            'status' => 'success',
            'message' => 'Clock-in successful at ' . now(),
        ]);
    }

}
