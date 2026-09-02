<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Programme;
use Illuminate\Http\Request;

class ProgrammeController extends Controller
{
    public function index()
    {
        $programmes = Programme::with('department')->withCount('trades')->orderBy('name')->paginate(15);
        $departments = Department::orderBy('name')->get();
        return view('admin.programmes.index', compact('programmes', 'departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);
        Programme::create($data);
        return back()->with('status', 'Programme added.');
    }

    public function update(Request $request, Programme $programme)
    {
        $data = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);
        $programme->update($data);
        return back()->with('status', 'Programme updated.');
    }

    public function destroy(Programme $programme)
    {
        $programme->delete();
        return back()->with('status', 'Programme deleted.');
    }
}
