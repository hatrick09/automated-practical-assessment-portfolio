<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = User::where('role', 'student')->count();

        $totalAssessmentsThisTerm = Assessment::whereDate('date', '>=', now()->subMonths(4))->count();

        $avgByTrade = Trade::query()
            ->select('trades.id', 'trades.trade_name')
            ->selectRaw('ROUND(AVG(assessments.score), 1) as avg_score')
            ->join('courses', 'courses.trade_id', '=', 'trades.id')
            ->join('rubrics', 'rubrics.course_id', '=', 'courses.id')
            ->join('assessments', 'assessments.rubric_id', '=', 'rubrics.id')
            ->groupBy('trades.id', 'trades.trade_name')
            ->get();

        $avgByCourse = Course::query()
            ->select('courses.id', 'courses.course_name')
            ->selectRaw('ROUND(AVG(assessments.score), 1) as avg_score')
            ->join('rubrics', 'rubrics.course_id', '=', 'courses.id')
            ->join('assessments', 'assessments.rubric_id', '=', 'rubrics.id')
            ->groupBy('courses.id', 'courses.course_name')
            ->get();

        $recentAssessments = Assessment::with(['student', 'instructor', 'rubric.course'])
            ->latest('date')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents', 'totalAssessmentsThisTerm', 'avgByTrade', 'avgByCourse', 'recentAssessments'
        ));
    }
}
