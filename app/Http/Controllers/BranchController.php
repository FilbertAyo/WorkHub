<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::withCount('users')->get();

        return view('settings.branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $id = $request->branch_id ? Hashids::decode($request->branch_id)[0] : null;

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'location_url' => 'required|url|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        if ($id) {
            $branch = Branch::findOrFail($id);
            $branch->update($validatedData);
            $message = 'Branch updated successfully';
        } else {
            Branch::create($validatedData);
            $message = 'Branch created successfully';
        }

        return redirect()->back()->with('success', $message);
    }


    /**
     * Display the specified resource.
     */
    public function show($hashid)
    {
        $id = Hashids::decode($hashid);

        $branch = Branch::withCount('users')->findOrFail($id[0]);

        return view('settings.branches.view', compact('branch'));
    }

    public function update(Request $request, $hashid)
    {
        $id = Hashids::decode($hashid)[0] ?? null;
        $branch = Branch::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'location_url' => 'required|url|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        $branch->update($validatedData);

        return redirect()->back()->with('success', 'Branch updated successfully');
    }

    public function destroy($hashid)
    {
        $id = Hashids::decode($hashid);

        $branch = Branch::find($id[0]);

        if ($branch) {
            $branch->delete();

            return redirect()->back()->with('success', 'Branch deleted successfully');
        } else {
            // Redirect back with error message if branch not found
            return redirect()->back()->with('error', 'Branch not found');
        }
    }
}
