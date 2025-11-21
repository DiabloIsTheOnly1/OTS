<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use App\Models\Department;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['department', 'branches'])->orderBy('id')->get();
        $departments = Department::orderBy('department_name')->get();
        $branches = Branch::orderBy('name')->get();

        return view('settings.user', compact('users', 'departments', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'username'       => 'required|string|max:100|unique:users,username',
            'password'       => 'required|string|min:4',
            'department_id'  => 'nullable|exists:departments,id',
            'branches'       => 'array',
        ]);

        $user = User::create([
            'name'          => $request->name,
            'username'      => $request->username,
            'password'      => $request->password,
            'department_id' => $request->department_id,
        ]);

        $user->branches()->sync($request->branches ?? []);

        return redirect()->back()->with('success', 'User created successfully!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:255',
            'username'      => 'required|string|max:100|unique:users,username,' . $user->id,
            'password'      => 'nullable|string|min:4',
            'department_id' => 'nullable|exists:departments,id',
            'branches'      => 'array',
        ]);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->department_id = $request->department_id;

        if ($request->filled('password')) {
            $user->password = $request->password;
        }

        $user->save();
        $user->branches()->sync($request->branches ?? []);

        return redirect()->back()->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'User deleted.');
    }
}
