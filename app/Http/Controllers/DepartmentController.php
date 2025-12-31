<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request ->query('search');

         $departments = Department::when($search, function ($query, $search) {
                $query->where('department_name', 'LIKE', "%$search%");
          })
          ->orderBy('department_name')
          ->paginate(10)
          ->withQueryString();

          return view('settings.department', compact('departments'));
    }

    public function store(Request $request)
    {
       $request->validate([
            'department_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (Department::whereRaw('LOWER(department_name) = ?', [strtolower($value)])->exists()) {
                        $fail('This department already exists.');
                    }
                }
            ],
        ]);

        Department::create([
            'department_name' => trim($request->department_name),
        ]);

        return redirect()->back()->with('success', 'Department created successfully!');
    }

    public function update(Request $request, $id)
    {
    $request->validate([
            'department_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($id) {
                    if (
                        Department::whereRaw('LOWER(department_name) = ?', [strtolower($value)])
                            ->where('id', '!=', $id)
                            ->exists()
                    ) {
                        $fail('This department already exists.');
                    }
                }
            ],
        ]);

        $department = Department::findOrFail($id);
        $department->update([
            'department_name' => trim($request->department_name),
        ]);
        return redirect()->back()->with('success', 'Department updated successfully!');
    }

    public function destroy($id){
    $department = Department::findOrFail($id);

        // Optional: check if department has staff linked
        if ($department->staff()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete department because it has staff linked.');
        }

        $department->delete();
        return redirect()->back()->with('success', 'Department deleted.');
    }
}
