@extends('layouts.app')
@section('title', 'HOD Dashboard')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="section-title mb-0">{{ $department->name }} Department</h1>
        <a href="{{ route('hod.approvals.index') }}" class="btn btn-teal">Review Pending Approvals ({{ $pendingCount }})</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card-tvet">
                <div class="text-muted small mb-1">Instructors</div>
                <div class="stat-counter">{{ $instructorCount }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-tvet">
                <div class="text-muted small mb-1">Students</div>
                <div class="stat-counter">{{ $studentCount }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-tvet">
                <div class="text-muted small mb-1">Avg. Approved Score</div>
                <div class="stat-counter">{{ $avgScorePct ? round($avgScorePct, 1) : 0 }}%</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-tvet">
                <div class="text-muted small mb-1">Pending Approvals</div>
                <div class="stat-counter">{{ $pendingCount }}</div>
            </div>
        </div>
    </div>

    <div class="card-tvet mb-4">
        <div class="section-title mb-3" style="font-size:16px;">Trades in This Department</div>
        <table class="table table-tvet align-middle">
            <thead><tr><th>Trade</th><th>Programme</th><th>Courses</th></tr></thead>
            <tbody>
            @forelse($trades as $trade)
                <tr>
                    <td>{{ $trade->trade_name }}</td>
                    <td>{{ $trade->programme->name ?? '—' }}</td>
                    <td>{{ $trade->courses_count }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-muted text-center py-3">No trades linked to this department yet. Ask an admin to link one under Trades.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-tvet">
        <div class="section-title mb-3" style="font-size:16px;">Recent Pending Assessments</div>
        <table class="table table-tvet align-middle">
            <thead><tr><th>Student</th><th>Course</th><th>Criterion</th><th>Score</th><th>Instructor</th><th>Date</th><th></th></tr></thead>
            <tbody>
            @forelse($pendingApprovals as $a)
                <tr>
                    <td>{{ $a->student->name }}</td>
                    <td>{{ $a->rubric->course->course_name }}</td>
                    <td>{{ $a->rubric->criterion }}</td>
                    <td><span class="badge-teal">{{ $a->score }}/{{ $a->rubric->max_score }}</span></td>
                    <td>{{ $a->instructor->name }}</td>
                    <td>{{ $a->date->format('d M Y') }}</td>
                    <td class="text-end">
                        <form method="POST" action="{{ route('hod.approvals.approve', $a) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-teal">Approve</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-muted text-center py-3">Nothing pending — all caught up.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
