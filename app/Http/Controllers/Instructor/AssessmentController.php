<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssessmentController extends Controller
{
    // Step 1: pick a course (only ones assigned to this instructor).
    public function index(Request $request)
    {
        $courses = $request->user()->assignedCourses()->withCount('students')->orderBy('course_name')->get();
        return view('instructor.assessments.index', compact('courses'));
    }

    // Step 2: pick a student within that course + show the scoring form.
    public function create(Request $request, Course $course)
    {
        $this->authorizeCourse($request, $course);

        $students = $course->students()->orderBy('name')->get();
        $rubrics = $course->rubrics()->orderBy('id')->get();

        $selectedStudent = null;
        $studentSubmissions = collect();
        if ($request->filled('student_id')) {
            $selectedStudent = $students->firstWhere('id', $request->integer('student_id'));
            if ($selectedStudent) {
                $studentSubmissions = \App\Models\Submission::where('course_id', $course->id)
                    ->where('student_id', $selectedStudent->id)
                    ->with('rubric')
                    ->latest('submitted_at')
                    ->get();
            }
        }

        $currentSemester = Semester::where('is_current', true)->first();

        return view('instructor.assessments.create', compact('course', 'students', 'rubrics', 'selectedStudent', 'studentSubmissions', 'currentSemester'));
    }

    public function store(Request $request, Course $course)
    {
        $this->authorizeCourse($request, $course);

        $rubricIds = $course->rubrics()->pluck('id')->toArray();
        $studentIds = $course->students()->pluck('users.id')->toArray();
        $currentSemesterId = Semester::where('is_current', true)->value('id');

        $data = $request->validate([
            'student_id' => ['required', 'integer', \Illuminate\Validation\Rule::in($studentIds)],
            'date' => ['required', 'date'],
            'scores' => ['required', 'array'],
            'remarks' => ['array'],
            'evidence' => ['array'],
            'evidence.*' => ['array'],
            'evidence.*.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'evidence_description' => ['array'],
            'evidence_type' => ['array'],
            'evidence_type.*' => ['nullable', 'in:file,video_link,code_link'],
            'evidence_url' => ['array'],
            'evidence_url.*' => ['nullable', 'url', 'max:500'],
        ]);

        // Validate each score against its rubric's max_score, and that rubric belongs to this course.
        $rubrics = $course->rubrics()->whereIn('id', array_keys($data['scores']))->get()->keyBy('id');
        foreach ($data['scores'] as $rubricId => $score) {
            if (! isset($rubrics[$rubricId])) {
                return back()->withErrors(['scores' => 'Invalid rubric submitted.'])->withInput();
            }
            if ($score === '' || $score === null) {
                continue;
            }
            if (! is_numeric($score) || $score < 0 || $score > $rubrics[$rubricId]->max_score) {
                return back()->withErrors([
                    "scores.$rubricId" => "Score for '{$rubrics[$rubricId]->criterion}' must be between 0 and {$rubrics[$rubricId]->max_score}.",
                ])->withInput();
            }
        }

        DB::transaction(function () use ($request, $data, $rubrics, $currentSemesterId) {
            foreach ($data['scores'] as $rubricId => $score) {
                if ($score === '' || $score === null) {
                    continue;
                }

                $assessment = Assessment::create([
                    'student_id' => $data['student_id'],
                    'rubric_id' => $rubricId,
                    'instructor_id' => $request->user()->id,
                    'score' => (int) $score,
                    'remarks' => $data['remarks'][$rubricId] ?? null,
                    'date' => $data['date'],
                    'semester_id' => $currentSemesterId,
                    // New assessments always start pending; an HOD (or admin) must approve
                    // before they count toward the student's official portfolio/report stats.
                    'status' => 'pending',
                ]);

                // File evidence (multiple files allowed per criterion).
                $files = $request->file("evidence.$rubricId") ?? [];
                foreach ($files as $file) {
                    $path = $file->store('evidence', 'public');
                    $assessment->evidence()->create([
                        'file_path' => $path,
                        'description' => $data['evidence_description'][$rubricId] ?? null,
                        'type' => 'file',
                    ]);
                }

                // Optional single video/code link per criterion.
                $linkType = $data['evidence_type'][$rubricId] ?? null;
                $linkUrl = $data['evidence_url'][$rubricId] ?? null;
                if ($linkType && in_array($linkType, ['video_link', 'code_link'], true) && $linkUrl) {
                    $assessment->evidence()->create([
                        'file_path' => $linkUrl,
                        'description' => $data['evidence_description'][$rubricId] ?? null,
                        'type' => $linkType,
                    ]);
                }
            }
        });

        return redirect()
            ->route('instructor.assessments.create', $rubrics->first()?->course_id ?? $request->route('course'))
            ->with('status', 'Assessment submitted and sent for HOD approval. It will appear on the student\'s portfolio once approved.');
    }

    protected function authorizeCourse(Request $request, Course $course): void
    {
        $isAssigned = $request->user()->assignedCourses()->where('courses.id', $course->id)->exists();
        abort_unless($isAssigned, 403, 'You are not assigned to this course.');
    }
}
