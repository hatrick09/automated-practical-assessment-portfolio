<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Rubric;
use Illuminate\Http\Request;

class RubricController extends Controller
{
    public function index(Request $request)
    {
        $query = Rubric::with('course.trade');
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->integer('course_id'));
        }
        $rubrics = $query->orderBy('course_id')->paginate(20)->withQueryString();
        $courses = Course::orderBy('course_name')->get();
        return view('admin.rubrics.index', compact('rubrics', 'courses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'criterion' => ['required', 'string', 'max:255'],
            'max_score' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);
        Rubric::create($data);
        return back()->with('status', 'Rubric added.');
    }

    public function update(Request $request, Rubric $rubric)
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'criterion' => ['required', 'string', 'max:255'],
            'max_score' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);
        $rubric->update($data);
        return back()->with('status', 'Rubric updated.');
    }

    public function destroy(Rubric $rubric)
    {
        $rubric->delete();
        return back()->with('status', 'Rubric deleted.');
    }
}
