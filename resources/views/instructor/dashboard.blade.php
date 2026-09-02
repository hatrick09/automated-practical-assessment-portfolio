@extends('layouts.app')
@section('title', 'Instructor Dashboard')
@section('content')
    <h1 class="section-title mb-4">Welcome, {{ auth()->user()->name }}</h1>

    <div class="row g-3 mb-4">
        @forelse($courses as $course)
            <div class="col-md-4">
                <div class="card-tvet h-100">
                    <div class="fw-bold mb-1">{{ $course->course_name }}</div>
                    <div class="text-muted small mb-3">{{ $course->students_count }} students enrolled</div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('instructor.assessments.create', $course) }}" class="btn btn-sm btn-teal">Score Students</a>
                        <a href="{{ route('instructor.roster.edit', $course) }}" class="btn btn-sm btn-outline-teal">Attendance &amp; Feedback</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><p class="text-muted">You have not been assigned to any courses yet. Contact an administrator.</p></div>
        @endforelse
    </div>

    <div class="card-tvet">
        <div class="section-title mb-3" style="font-size:16px;">Your Recent Scoring</div>
        <table class="table table-tvet align-middle">
            <thead><tr><th>Student</th><th>Course</th><th>Criterion</th><th>Score</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
            @forelse($recentAssessments as $a)
                <tr>
                    <td>{{ $a->student->name }}</td>
                    <td>{{ $a->rubric->course->course_name }}</td>
                    <td>{{ $a->rubric->criterion }}</td>
                    <td><span class="badge-teal">{{ $a->score }}/{{ $a->rubric->max_score }}</span></td>
                    <td>
                        @if($a->status === 'approved')
                            <span class="verified-badge">Approved</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis">Pending HOD</span>
                        @endif
                    </td>
                    <td>{{ $a->date->format('d M Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted text-center py-3">No assessments submitted yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
