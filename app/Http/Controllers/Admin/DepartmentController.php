<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount(['programmes', 'users'])->orderBy('name')->paginate(15);
        return view('admin.departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255']]);
        Department::create($data);
        return back()->with('status', 'Department added.');
    }

    public function update(Request $request, Department $department)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255']]);
        $department->update($data);
        return back()->with('status', 'Department updated.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return back()->with('status', 'Department deleted.');
    }
}
