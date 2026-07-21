<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use App\Models\Department;
use App\Models\AccessLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {


        $perPage = request('per_page', 15);

        $query = User::with(['departments', 'branches'])->orderBy('id');

        // 🔍 Apply filters
        if (request('search')) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . request('search') . '%')
                    ->orWhere('username', 'like', '%' . request('search') . '%');
            });
        }

        if (request('department_id')) {
            $query->where('department_id', request('department_id'));
        }

        if (request('branch_id')) {
            $query->whereHas('branches', function ($q) {
                $q->where('branch.id', request('branch_id'));
            });
        }

        if (request('access_level_id')) {
            $query->where('access_level_id', request('access_level_id'));
        }

        $users = $query->paginate($perPage)->withQueryString();
        $departments = Department::orderBy('department_name')->get();
        $branches = Branch::orderBy('name')->get();
        $accessLevels = AccessLevel::orderBy('name')->get();

        return view(
            'settings.user',
            compact('users', 'departments', 'branches', 'accessLevels')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username',
            'password' => 'required|string|min:1',
            'departments' => 'nullable|array',
            'departments.*' => 'exists:departments,id',
            'branches' => 'array',
            'access_all_departments' => 'boolean',
            'access_level_id' => 'required|exists:access_levels,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => $request->password,
            'access_all_departments' => $request->boolean('access_all_departments'),
            'access_level_id' => $request->access_level_id,
        ]);

        $user->departments()->sync($request->departments ?? []);
        $user->branches()->sync($request->branches ?? []);

        return redirect()->back()->with('success', 'User created successfully!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:4',
            'departments' => 'nullable|array',
            'departments.*' => 'exists:departments,id',
            'branches' => 'array',
            'access_all_departments' => 'boolean',
            'access_level_id' => 'required|exists:access_levels,id',
        ]);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->departments()->sync($request->departments ?? []);
        $user->access_all_departments = $request->access_all_departments ? 1 : 0;
        $user->access_level_id = $request->access_level_id;

        if ($request->filled('password')) {
            $user->password = $request->password;
        }

        // Get selected departments
        $departments = $request->departments ?? [];

        // If this user has never been migrated,
// keep the old department_id as well.
        if ($user->departments()->count() == 0 && !empty($user->department_id)) {
            $departments[] = $user->department_id;
        }

        // Remove duplicates
        $departments = array_unique($departments);

        // Save
        $user->departments()->sync($departments);

        $user->save();
        $user->branches()->sync($request->branches ?? []);
        $user->departments()->sync($request->departments ?? []);

        return redirect()->back()->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'User deleted.');
    }
}
