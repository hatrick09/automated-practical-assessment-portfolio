<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class RosterController extends Controller
{
    public function edit(Request $request, Course $course)
    {
        $this->authorizeCourse($request, $course);

        $students = $course->students()->orderBy('name')->get();

        return view('instructor.roster.edit', compact('course', 'students'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorizeCourse($request, $course);

        $data = $request->validate([
            'attendance' => ['array'],
            'attendance.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'feedback' => ['array'],
            'feedback.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $studentIds = $course->students()->pluck('users.id');

        foreach ($studentIds as $studentId) {
            $course->students()->updateExistingPivot($studentId, [
                'attendance_percentage' => $data['attendance'][$studentId] ?? null,
                'overall_feedback' => $data['feedback'][$studentId] ?? null,
            ]);
        }

        return back()->with('status', 'Attendance and feedback updated.');
    }

    protected function authorizeCourse(Request $request, Course $course): void
    {
        $isAssigned = $request->user()->assignedCourses()->where('courses.id', $course->id)->exists();
        abort_unless($isAssigned, 403, 'You are not assigned to this course.');
    }
}
