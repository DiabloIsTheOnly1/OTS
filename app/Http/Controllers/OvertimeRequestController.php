<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OvertimeRequest;
use App\Models\Branch;
use App\Models\Department;

class OvertimeRequestController extends Controller
{
    // Show the Employee Overtime Form
    public function index()
    {
        $branches = Branch::all();
        $departments = Department::all();

        return view('overtime.form', compact('branches', 'departments'));
    }

    // Store the submitted Overtime Request
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'date' => 'required|date',
            'work_description' => 'required|string',
        ]);

        OvertimeRequest::create($request->all());

        return redirect()->back()->with('success', 'Overtime request submitted successfully!');
    }
}
