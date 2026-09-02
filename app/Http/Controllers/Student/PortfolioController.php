<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Portfolio;
use App\Models\User;
use App\Support\GradeScale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PortfolioController extends Controller
{
    protected function competencies(User $student)
    {
        // All assessments (pending + approved), newest first. Pending ones are shown
        // distinctly in the UI and excluded from stats/GPA until an HOD approves them.
        return Assessment::with(['rubric.course.trade', 'instructor', 'evidence', 'semester'])
            ->where('student_id', $student->id)
            ->orderByDesc('date')
            ->get();
    }

    protected function stats($assessments): array
    {
        $approved = $assessments->where('status', 'approved');

        return [
            'tasks_assessed' => $approved->count(),
            'pending_count' => $assessments->where('status', 'pending')->count(),
            'average_score_pct' => $approved->count()
                ? round($approved->avg(fn ($a) => $a->rubric->max_score > 0 ? ($a->score / $a->rubric->max_score) * 100 : 0), 1)
                : 0,
            'competencies_verified' => $approved->pluck('rubric_id')->unique()->count(),
        ];
    }

    // Builds the per-course report-card rows (grade, attendance, feedback) and overall CGPA.
    protected function reportCard(User $student): array
    {
        $courses = $student->enrolledCourses()->with('trade')->get();

        $rows = $courses->map(function ($course) use ($student) {
            $approved = Assessment::approved()
                ->where('student_id', $student->id)
                ->whereHas('rubric', fn ($q) => $q->where('course_id', $course->id))
                ->with('rubric')
                ->get();

            $pct = $approved->count()
                ? round($approved->avg(fn ($a) => $a->rubric->max_score > 0 ? ($a->score / $a->rubric->max_score) * 100 : 0), 1)
                : null;

            return [
                'course' => $course,
                'percent' => $pct,
                'letter' => $pct !== null ? GradeScale::letter($pct) : null,
                'point' => $pct !== null ? GradeScale::point($pct) : null,
                'attendance' => $course->pivot->attendance_percentage,
                'feedback' => $course->pivot->overall_feedback,
                'credit_hours' => $course->credit_hours ?? 3,
            ];
        });

        $graded = $rows->filter(fn ($r) => $r['point'] !== null);
        $totalCredits = $graded->sum('credit_hours');
        $cgpa = $totalCredits > 0
            ? round($graded->sum(fn ($r) => $r['point'] * $r['credit_hours']) / $totalCredits, 2)
            : null;

        $avgAttendance = $rows->pluck('attendance')->filter(fn ($v) => $v !== null)->avg();

        return [
            'rows' => $rows,
            'cgpa' => $cgpa,
            'average_attendance' => $avgAttendance !== null ? round($avgAttendance, 1) : null,
        ];
    }

    public function show(Request $request)
    {
        $student = $request->user();
        $assessments = $this->competencies($student);
        $stats = $this->stats($assessments);
        $report = $this->reportCard($student);

        return view('student.portfolio', compact('student', 'assessments', 'stats', 'report'));
    }

    // Admins/instructors/HODs viewing a specific student's portfolio; students may only view their own.
    public function showFor(Request $request, User $user)
    {
        if ($request->user()->isStudent() && $request->user()->id !== $user->id) {
            abort(403, 'You may only view your own portfolio.');
        }

        $assessments = $this->competencies($user);
        $stats = $this->stats($assessments);
        $report = $this->reportCard($user);

        return view('student.portfolio', ['student' => $user, 'assessments' => $assessments, 'stats' => $stats, 'report' => $report]);
    }

    public function exportPdf(Request $request)
    {
        $student = $request->user();
        $assessments = $this->competencies($student);
        $stats = $this->stats($assessments);
        $report = $this->reportCard($student);

        // Ensure a public share link exists so the PDF's QR code always resolves.
        $portfolio = Portfolio::firstOrCreate(['student_id' => $student->id]);
        if (! $portfolio->share_token) {
            $portfolio->share_token = Str::random(32);
        }
        $portfolio->generated_date = now();
        $portfolio->save();

        $qrUrl = route('portfolio.public', $portfolio->share_token);

        $pdf = Pdf::loadView('student.portfolio-pdf', compact('student', 'assessments', 'stats', 'report', 'qrUrl', 'portfolio'));

        return $pdf->download('portfolio-'.Str::slug($student->name).'.pdf');
    }

    public function share(Request $request)
    {
        $student = $request->user();
        $portfolio = Portfolio::firstOrCreate(
            ['student_id' => $student->id],
            ['generated_date' => now()]
        );

        if (! $portfolio->share_token) {
            $portfolio->share_token = Str::random(32);
            $portfolio->save();
        }

        return back()->with('status', 'Public share link generated.')->with('share_url', route('portfolio.public', $portfolio->share_token));
    }

    public function revokeShare(Request $request)
    {
        Portfolio::where('student_id', $request->user()->id)->update(['share_token' => null]);
        return back()->with('status', 'Public share link revoked.');
    }

    public function publicShow(string $token)
    {
        $portfolio = Portfolio::where('share_token', $token)->firstOrFail();
        $student = $portfolio->student;
        $assessments = $this->competencies($student)->where('status', 'approved');
        $report = $this->reportCard($student);

        return view('student.portfolio-public', compact('student', 'assessments', 'report'));
    }
}
