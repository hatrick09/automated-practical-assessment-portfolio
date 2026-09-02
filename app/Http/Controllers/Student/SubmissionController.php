<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user();
        $courses = $student->enrolledCourses()->with('rubrics')->orderBy('course_name')->get();
        $submissions = $student->submissions()->with(['course', 'rubric'])->latest('submitted_at')->paginate(10);

        return view('student.submissions.index', compact('courses', 'submissions'));
    }

    public function store(Request $request)
    {
        $student = $request->user();
        $courseIds = $student->enrolledCourses()->pluck('courses.id')->toArray();

        $data = $request->validate([
            'course_id' => ['required', 'integer', \Illuminate\Validation\Rule::in($courseIds)],
            'rubric_id' => ['nullable', 'integer', 'exists:rubrics,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type' => ['required', 'in:file,video_link,code_link'],
            'file' => ['required_if:type,file', 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,zip', 'max:10240'],
            'url' => ['required_unless:type,file', 'nullable', 'url', 'max:500'],
        ]);

        // Guard: if a rubric was chosen, make sure it actually belongs to the selected course.
        if (! empty($data['rubric_id'])) {
            $belongs = \App\Models\Rubric::where('id', $data['rubric_id'])->where('course_id', $data['course_id'])->exists();
            if (! $belongs) {
                return back()->withErrors(['rubric_id' => 'That task does not belong to the selected course.'])->withInput();
            }
        }

        $filePath = $data['type'] === 'file'
            ? $request->file('file')->store('submissions', 'public')
            : $data['url'];

        Submission::create([
            'student_id' => $student->id,
            'course_id' => $data['course_id'],
            'rubric_id' => $data['rubric_id'] ?? null,
            'title' => $data['title'],
            'file_path' => $filePath,
            'description' => $data['description'] ?? null,
            'status' => 'pending',
            'submitted_at' => now(),
            'type' => $data['type'],
        ]);

        return back()->with('status', 'Your work has been submitted to your instructor for review.');
    }

    public function destroy(Request $request, Submission $submission)
    {
        abort_unless($submission->student_id === $request->user()->id, 403);
        if ($submission->type === 'file') {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($submission->file_path);
        }
        $submission->delete();

        return back()->with('status', 'Submission removed.');
    }
}
