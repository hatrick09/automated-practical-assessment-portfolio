<?php

namespace App\Http\Controllers\Hod;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $hod = $request->user();
        $department = $hod->department;
        abort_unless($department, 403, 'You are not assigned to a department yet.');

        $courseIds = \App\Models\Course::whereHas('trade.programme', fn ($q) => $q->where('department_id', $department->id))
            ->pluck('id');

        $status = $request->get('status', 'pending');

        $assessments = Assessment::whereHas('rubric', fn ($q) => $q->whereIn('course_id', $courseIds))
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->with(['student', 'instructor', 'rubric.course'])
            ->latest('date')
            ->paginate(20)
            ->withQueryString();

        return view('hod.approvals.index', compact('assessments', 'status'));
    }

    public function approve(Request $request, Assessment $assessment)
    {
        $this->authorizeInDepartment($request, $assessment);

        $assessment->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('status', "Assessment for {$assessment->student->name} approved.");
    }

    protected function authorizeInDepartment(Request $request, Assessment $assessment): void
    {
        $department = $request->user()->department;
        abort_unless($department, 403);

        $belongs = $assessment->rubric->course->trade->programme?->department_id === $department->id;
        abort_unless($belongs, 403, 'This assessment is outside your department.');
    }
}
