<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Branch;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;


class StaffController extends Controller
{
    // Show list
    public function index()
    {
        /** @var User $user */
        $user = auth()->user();

        // Branch access (always enforced)
        $userBranchIds = $user->branches->pluck('id')->toArray();

        // Department access
        $userDepartmentId = $user->department_id;
        $canAccessAllDepartments = $user->access_all_departments == 1;

        // Staff list
        $staff = Staff::with(['branch', 'department'])
            ->whereIn('branch_id', $userBranchIds)
            ->when(!$canAccessAllDepartments, function ($query) use ($userDepartmentId) {
                $query->where('department_id', $userDepartmentId);
            })
            ->get();

        // Branch dropdown: only user's allowed branches
        $branches = Branch::whereIn('id', $userBranchIds)
            ->orderBy('name')
            ->get();

        // Department dropdown
        $departments = Department::when(!$canAccessAllDepartments, function ($query) use ($userDepartmentId) {
            $query->where('id', $userDepartmentId);
        })
            ->orderBy('department_name')
            ->get();

        return view('settings.staff', compact('staff', 'departments', 'branches'));
    }


    // Show create form
    public function create()
    {
        $branches = Branch::all();
        $departments = Department::all();

        return view('settings.staff', compact('branches', 'departments'));
    }

    // Store new staff
    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'branch_id' => 'required|exists:branch,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        Staff::create($validated);

        return redirect()->back()->with('success', 'Staff added successfully.');
    }

    // Show edit form
    public function edit($id)
    {
        $staff = Staff::findOrFail($id);
        $branches = Branch::all();
        $departments = Department::all();

        return view('settings.staff', compact('staff', 'branches', 'departments'));
    }

    // Update record
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'staff_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'branch_id' => 'required|exists:branch,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        $staff = Staff::findOrFail($id);
        $staff->update($validated);

        return redirect()->route('staff.index')->with('success', 'Staff updated successfully.');
    }

    // Delete record
    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        $staff->delete();

        return redirect()->route('settings.staff')->with('success', 'Staff deleted successfully.');
    }
}
