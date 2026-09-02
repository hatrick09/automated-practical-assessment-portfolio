<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\Department;
use App\Models\Semester;
use App\Models\Trade;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    protected function filteredQuery(Request $request)
    {
        $query = Assessment::query()->with(['student', 'instructor', 'rubric.course.trade.programme.department', 'semester']);

        if ($request->filled('department_id')) {
            $deptId = $request->integer('department_id');
            $query->whereHas('rubric.course.trade.programme', fn ($q) => $q->where('department_id', $deptId));
        }
        if ($request->filled('trade_id')) {
            $query->whereHas('rubric.course', fn ($q) => $q->where('trade_id', $request->integer('trade_id')));
        }
        if ($request->filled('course_id')) {
            $query->whereHas('rubric', fn ($q) => $q->where('course_id', $request->integer('course_id')));
        }
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->integer('semester_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->date('to'));
        }

        return $query->orderByDesc('date');
    }

    public function index(Request $request)
    {
        $assessments = $this->filteredQuery($request)->paginate(25)->withQueryString();
        $trades = Trade::orderBy('trade_name')->get();
        $courses = Course::orderBy('course_name')->get();
        $departments = Department::orderBy('name')->get();
        $semesters = Semester::with('academicYear')->orderByDesc('id')->get();

        return view('admin.reports.index', compact('assessments', 'trades', 'courses', 'departments', 'semesters'));
    }

    public function export(Request $request): StreamedResponse
    {
        $assessments = $this->filteredQuery($request)->get();

        $filename = 'competency-report-'.now()->format('Ymd_His').'.csv';

        $callback = function () use ($assessments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Student', 'Department', 'Trade', 'Course', 'Criterion', 'Score', 'Max Score', 'Grade', 'Status', 'Instructor', 'Semester', 'Date', 'Remarks']);
            foreach ($assessments as $a) {
                $pct = $a->rubric->max_score > 0 ? ($a->score / $a->rubric->max_score) * 100 : 0;
                fputcsv($handle, [
                    $a->student->name ?? '',
                    $a->rubric->course->trade->programme->department->name ?? '',
                    $a->rubric->course->trade->trade_name ?? '',
                    $a->rubric->course->course_name ?? '',
                    $a->rubric->criterion ?? '',
                    $a->score,
                    $a->rubric->max_score ?? '',
                    \App\Support\GradeScale::letter($pct),
                    ucfirst($a->status),
                    $a->instructor->name ?? '',
                    $a->semester?->label() ?? '',
                    optional($a->date)->format('Y-m-d'),
                    $a->remarks,
                ]);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }
}
