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
    public function index(Request $request)
    {
       $search = $request ->query('search');

         $branches = Branch::when($search, function ($query, $search) {
                $query->where('name', 'LIKE', "%$search%");
          })
          ->orderBy('name')
          ->paginate(10)
          ->withQueryString();

          return view('settings.branch', compact('branches'));
    }

    /**
     * Store a new branch
     */
    public function store(Request $request)
    {
      $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
            function ($attribute, $value, $fail) {
                if (Branch::whereRaw('LOWER(name) = ?', [strtolower($value)])->exists()) {
                    $fail('This branch already exists.');
                }
            }
        ],
    ]);

    Branch::create([
        'name' => trim($request->name),
    ]);

    return redirect()->back()->with('success', 'Branch created successfully.');
    }

    /**
     * Update branch
     */
    public function update(Request $request, $id)
    {
       $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
            function ($attribute, $value, $fail) use ($id) {
                if (
                    Branch::whereRaw('LOWER(name) = ?', [strtolower($value)])
                        ->where('id', '!=', $id)
                        ->exists()
                ) {
                    $fail('This branch already exists.');
                }
            }
        ],
    ]);

    $branch = Branch::findOrFail($id);
    $branch->name = trim($request->name);
    $branch->save();

    return redirect()->back()->with('success', 'Branch updated successfully!');
    }

    /**
     * Delete branch
     */
    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);

        if (
            $branch->staff()->exists() ||
            $branch->users()->exists() ||
            $branch->overtimeRequests()->exists()
        ) {
            return redirect()->back()
                ->with('error', 'Cannot delete branch because it is already linked to staff, users, or overtime requests.');
        }

        $branch->delete();

        return redirect()->back()->with('success', 'Branch deleted successfully.');
    }
}
