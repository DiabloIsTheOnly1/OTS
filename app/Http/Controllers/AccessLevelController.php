<?php

namespace App\Http\Controllers;

use App\Models\AccessLevel;
use Illuminate\Http\Request;

class AccessLevelController extends Controller
{
    public function index()
    {
        $levels = AccessLevel::withCount('users')->get();
        return view('settings.access-levels', compact('levels'));
    }

    public function create()
    {
        return view('settings.access-levels');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        AccessLevel::create($request->only([
            'name',
            'access_level',
            'user',
            'branch_settings',
            'department_settings',
            'staff_settings',
            'manage_request',
            'hod_approval',
            'hq_approval',
        ]));

        return redirect()->route('settings.access-level')->with('success', 'Access Level created successfully.');
    }

    public function edit($id)
    {
        $level = AccessLevel::findOrFail($id);
        return view('settings.access-levels', compact('level'));
    }

    public function update(Request $request, $id)
    {
        $level = AccessLevel::findOrFail($id);

        $level->update([
            'name' => $request->name,
            'access_level' => $request->access_level ? 1 : 0,
            'user' => $request->user ? 1 : 0,
            'branch_settings' => $request->branch_settings ? 1 : 0,
            'department_settings' => $request->department_settings ? 1 : 0,
            'staff_settings' => $request->staff_settings ? 1 : 0,
            'manage_request' => $request->manage_request ? 1 : 0,
            'hod_approval' => $request->hod_approval ? 1 : 0,
            'hq_approval' => $request->hq_approval ? 1 : 0,
        ]);

        return redirect()->route('settings.access-level')
            ->with('success', 'Access Level updated successfully.');
    }


    public function destroy($id)
    {
        AccessLevel::destroy($id);
        return redirect()->route('settings.access-level')->with('success', 'Access Level deleted.');
    }
}
