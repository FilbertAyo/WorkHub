<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::withCount('users')->get();

        return view('settings.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $id = $request->department_id ? Hashids::decode($request->department_id)[0] : null;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        if ($id) {
            $department = Department::findOrFail($id);
            $department->update($validated);
            $message = 'Department updated successfully';
        } else {
            Department::create($validated);
            $message = 'Department created successfully';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show($hashid)
    {
        $id = Hashids::decode($hashid);
        $department = Department::withCount('users')->findOrFail($id[0]);
        $users = $department->users;

        return view('settings.departments.view', compact('department', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $hashid)
    {
        $id = Hashids::decode($hashid)[0] ?? null;
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $department->update($validated);

        return redirect()->back()->with('success', 'Department updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($hashid)
    {
        $id = Hashids::decode($hashid);

        $department = Department::find($id[0]);

        if ($department) {
            $department->delete();

            return redirect()->back()->with('success', 'Department deleted successfully');
        } else {
            return redirect()->back()->with('error', 'Department not found');
        }
    }
}
