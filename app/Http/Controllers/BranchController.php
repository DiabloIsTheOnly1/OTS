<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;

class BranchController extends Controller
{
    /**
     * List all branches
     */
    public function index()
    {
        $branches = Branch::orderBy('id', 'asc')->get();

        return view('settings.branch', compact('branches'));
    }

    /**
     * Store a new branch
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch_name' => 'required|string|max:255',
        ]);

        Branch::create([
            'branch_name' => $request->branch_name,
        ]);

        return redirect()->back()->with('success', 'Branch created successfully.');
    }

    /**
     * Update branch
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'branch_name' => 'required|string|max:255',
        ]);

        $branch = Branch::findOrFail($id);
        $branch->update([
            'branch_name' => $request->branch_name,
        ]);

        return redirect()->back()->with('success', 'Branch updated successfully.');
    }

    /**
     * Delete branch
     */
    public function destroy($id)
    {
        Branch::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Branch deleted.');
    }
}
