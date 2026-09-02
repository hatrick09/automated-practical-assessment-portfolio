<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Trade;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('trade')->withCount('rubrics')->orderBy('course_name')->paginate(15);
        $trades = Trade::orderBy('trade_name')->get();
        return view('admin.courses.index', compact('courses', 'trades'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'trade_id' => ['required', 'exists:trades,id'],
            'course_name' => ['required', 'string', 'max:255'],
            'course_code' => ['nullable', 'string', 'max:50'],
            'credit_hours' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);
        Course::create($data);
        return back()->with('status', 'Course added.');
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'trade_id' => ['required', 'exists:trades,id'],
            'course_name' => ['required', 'string', 'max:255'],
            'course_code' => ['nullable', 'string', 'max:50'],
            'credit_hours' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);
        $course->update($data);
        return back()->with('status', 'Course updated.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return back()->with('status', 'Course deleted.');
    }
}
