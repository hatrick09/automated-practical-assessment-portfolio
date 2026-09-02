@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="section-title mb-0">Administrator Dashboard</h1>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card-tvet">
                <div class="text-muted small mb-1">Total Students</div>
                <div class="stat-counter">{{ $totalStudents }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-tvet">
                <div class="text-muted small mb-1">Assessments (last 4 months)</div>
                <div class="stat-counter">{{ $totalAssessmentsThisTerm }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-tvet">
                <div class="text-muted small mb-1">Trades Covered</div>
                <div class="stat-counter">{{ $avgByTrade->count() }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card-tvet h-100">
                <div class="section-title mb-3" style="font-size:16px;">Average Score by Trade</div>
                @forelse($avgByTrade as $t)
                    <div class="d-flex justify-content-between small mb-1">
                        <span>{{ $t->trade_name }}</span><span class="fw-semibold">{{ $t->avg_score }}</span>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No data yet.</p>
                @endforelse
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-tvet h-100">
                <div class="section-title mb-3" style="font-size:16px;">Average Score by Course</div>
                @forelse($avgByCourse as $c)
                    <div class="d-flex justify-content-between small mb-1">
                        <span>{{ $c->course_name }}</span><span class="fw-semibold">{{ $c->avg_score }}</span>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No data yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card-tvet">
        <div class="section-title mb-3" style="font-size:16px;">Recent Assessments</div>
        <div class="table-responsive">
            <table class="table table-tvet align-middle">
                <thead><tr><th>Student</th><th>Course</th><th>Criterion</th><th>Score</th><th>Instructor</th><th>Date</th></tr></thead>
                <tbody>
                @forelse($recentAssessments as $a)
                    <tr>
                        <td>{{ $a->student->name }}</td>
                        <td>{{ $a->rubric->course->course_name }}</td>
                        <td>{{ $a->rubric->criterion }}</td>
                        <td><span class="badge-teal">{{ $a->score }}/{{ $a->rubric->max_score }}</span></td>
                        <td>{{ $a->instructor->name }}</td>
                        <td>{{ $a->date->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted text-center py-3">No assessments recorded yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
