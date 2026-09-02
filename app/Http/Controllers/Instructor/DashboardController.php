<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $instructor = $request->user();
        $courses = $instructor->assignedCourses()->withCount('students')->get();
        $recentAssessments = $instructor->assessmentsAsInstructor()
            ->with(['student', 'rubric.course'])
            ->latest('date')
            ->limit(10)
            ->get();

        return view('instructor.dashboard', compact('courses', 'recentAssessments'));
    }
}
