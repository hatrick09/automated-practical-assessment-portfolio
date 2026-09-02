<?php

namespace App\Http\Controllers\Hod;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Trade;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $hod = $request->user();
        $department = $hod->department;

        abort_unless($department, 403, 'You are not assigned to a department yet. Contact an administrator.');

        $trades = Trade::whereHas('programme', fn ($q) => $q->where('department_id', $department->id))
            ->with('programme')
            ->withCount('courses')
            ->get();

        $courseIds = \App\Models\Course::whereIn('trade_id', $trades->pluck('id'))->pluck('id');

        $instructorCount = \App\Models\User::where('role', 'instructor')->where('department_id', $department->id)->count();
        $studentCount = \App\Models\User::where('role', 'student')->where('department_id', $department->id)->count();

        $avgScorePct = Assessment::approved()
            ->whereHas('rubric', fn ($q) => $q->whereIn('course_id', $courseIds))
            ->join('rubrics', 'assessments.rubric_id', '=', 'rubrics.id')
            ->selectRaw('AVG(assessments.score / rubrics.max_score * 100) as avg_pct')
            ->value('avg_pct');

        $pendingApprovals = Assessment::where('status', 'pending')
            ->whereHas('rubric', fn ($q) => $q->whereIn('course_id', $courseIds))
            ->with(['student', 'instructor', 'rubric.course'])
            ->latest('date')
            ->limit(10)
            ->get();

        $pendingCount = Assessment::where('status', 'pending')
            ->whereHas('rubric', fn ($q) => $q->whereIn('course_id', $courseIds))
            ->count();

        return view('hod.dashboard', compact(
            'department', 'trades', 'instructorCount', 'studentCount', 'avgScorePct', 'pendingApprovals', 'pendingCount'
        ));
    }
}
