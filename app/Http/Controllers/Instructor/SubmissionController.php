<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $instructor = $request->user();
        $courseIds = $instructor->assignedCourses()->pluck('courses.id');

        $query = Submission::with(['student', 'course', 'rubric'])
            ->whereIn('course_id', $courseIds);

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->integer('course_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $submissions = $query->latest('submitted_at')->paginate(15)->withQueryString();
        $courses = $instructor->assignedCourses()->orderBy('course_name')->get();

        return view('instructor.submissions.index', compact('submissions', 'courses'));
    }

    public function markReviewed(Request $request, Submission $submission)
    {
        $isAssigned = $request->user()->assignedCourses()->where('courses.id', $submission->course_id)->exists();
        abort_unless($isAssigned, 403);

        $submission->update(['status' => 'reviewed']);

        return back()->with('status', 'Marked as reviewed.');
    }
}
