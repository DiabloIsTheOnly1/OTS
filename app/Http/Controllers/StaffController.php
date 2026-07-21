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
    public function index(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();
        $perPage = request('per_page', 15);

        // Branch access
        $userBranchIds = $user->branches->pluck('id')->toArray();

        // Department access
        $userDepartmentIds = $user->departments->pluck('id')->toArray();
        $canAccessAllDepartments = $user->access_all_departments;

        // Apply filters from request
        $filterBranch = $request->branch_id;
        $filterDept = $request->department_id;
        $search = $request->search;

        $staff = Staff::with(['branch', 'department'])
            ->whereIn('branch_id', $userBranchIds)

            ->when(
                !$canAccessAllDepartments && count($userDepartmentIds),
                fn($q) => $q->whereIn('department_id', $userDepartmentIds)
            )

            ->when($filterBranch, function ($query) use ($filterBranch) {
                $query->where('branch_id', $filterBranch);
            })

            ->when($filterDept, function ($query) use ($filterDept) {
                $query->where('department_id', $filterDept);
            })

            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('staff_name', 'LIKE', "%{$search}%")
                        ->orWhere('position', 'LIKE', "%{$search}%");
                });
            })

            ->orderBy('staff_name')
            ->paginate($perPage)
            ->withQueryString();

        // Dropdown options
        $branches = Branch::whereIn('id', $userBranchIds)
            ->orderBy('name')
            ->get();

        $departments = Department::when(
            !$canAccessAllDepartments && count($userDepartmentIds),
            fn($q) => $q->whereIn('id', $userDepartmentIds)
        )
            ->orderBy('department_name')
            ->get();

        return view('settings.staff', compact('staff', 'departments', 'branches'));
    }



    // Show create form
    public function create()
    {
        $user = auth()->user();

        $branches = Branch::whereIn(
            'id',
            $user->branches->pluck('id')
        )->get();

        $departments = $user->access_all_departments
            ? Department::orderBy('department_name')->get()
            : Department::whereIn(
                'id',
                $user->departments->pluck('id')
            )->orderBy('department_name')->get();

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

        $user = auth()->user();

        abort_unless(
            $user->branches->contains('id', $validated['branch_id']),
            403
        );

        if (
            !$user->access_all_departments &&
            !$user->departments->contains('id', $validated['department_id'])
        ) {
            abort(403);
        }

        Staff::create($validated);

        return redirect()->back()->with('success', 'Staff added successfully.');
    }

    // Show edit form
    public function edit($id)
    {
        $user = auth()->user();

        $staff = Staff::findOrFail($id);

        $branches = Branch::whereIn(
            'id',
            $user->branches->pluck('id')
        )->get();

        $departments = $user->access_all_departments
            ? Department::orderBy('department_name')->get()
            : Department::whereIn(
                'id',
                $user->departments->pluck('id')
            )->orderBy('department_name')->get();

        return view('settings.staff', compact(
            'staff',
            'branches',
            'departments'
        ));
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

        return redirect()->route('settings.staff')->with('success', 'Staff updated successfully.');
    }

    // Delete record
    public function destroy($id)
    {
        Staff::findOrFail($id)->delete();

        return redirect()->route('settings.staff')->with('success', 'Staff deleted successfully.');
    }

}
