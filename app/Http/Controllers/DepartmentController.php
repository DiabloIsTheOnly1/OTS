<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('id')->get();
        return view('settings.department', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_name' => 'required|string|max:255'
        ]);

        Department::create([
            'department_name' => $request->department_name
        ]);

        return redirect()->back()->with('success', 'Department created successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'department_name' => 'required|string|max:255'
        ]);

        $department = Department::findOrFail($id);
        $department->update([
            'department_name' => $request->department_name
        ]);

        return redirect()->back()->with('success', 'Department updated successfully!');
    }

    public function destroy($id)
    {
        Department::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Department deleted.');
    }
}
