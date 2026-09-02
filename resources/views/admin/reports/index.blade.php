@extends('layouts.app')
@section('title', 'Reports')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="section-title mb-0">Competency Attainment Report</h1>
        <a href="{{ route('admin.reports.export', request()->query()) }}" class="btn btn-outline-teal"><i class="bi bi-download me-1"></i>Export CSV</a>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-2">
            <select name="department_id" class="form-select">
                <option value="">All departments</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" @selected(request('department_id')==$department->id)>{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="trade_id" class="form-select">
                <option value="">All trades</option>
                @foreach($trades as $trade)
                    <option value="{{ $trade->id }}" @selected(request('trade_id')==$trade->id)>{{ $trade->trade_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="course_id" class="form-select">
                <option value="">All courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @selected(request('course_id')==$course->id)>{{ $course->course_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="semester_id" class="form-select">
                <option value="">All semesters</option>
                @foreach($semesters as $semester)
                    <option value="{{ $semester->id }}" @selected(request('semester_id')==$semester->id)>{{ $semester->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">Any status</option>
                <option value="approved" @selected(request('status')=='approved')>Approved</option>
                <option value="pending" @selected(request('status')=='pending')>Pending</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-teal w-100">Filter</button></div>
        <div class="col-md-3"><input type="date" name="from" value="{{ request('from') }}" class="form-control" placeholder="From"></div>
        <div class="col-md-3"><input type="date" name="to" value="{{ request('to') }}" class="form-control" placeholder="To"></div>
    </form>

    <div class="card-tvet">
        <div class="table-responsive">
            <table class="table table-tvet align-middle">
                <thead><tr><th>Student</th><th>Dept</th><th>Trade</th><th>Course</th><th>Criterion</th><th>Score</th><th>Grade</th><th>Status</th><th>Instructor</th><th>Semester</th><th>Date</th></tr></thead>
                <tbody>
                @forelse($assessments as $a)
                    @php $pct = $a->rubric->max_score > 0 ? ($a->score / $a->rubric->max_score) * 100 : 0; @endphp
                    <tr>
                        <td>{{ $a->student->name }}</td>
                        <td class="small text-muted">{{ $a->rubric->course->trade->programme->department->name ?? '—' }}</td>
                        <td>{{ $a->rubric->course->trade->trade_name }}</td>
                        <td>{{ $a->rubric->course->course_name }}</td>
                        <td>{{ $a->rubric->criterion }}</td>
                        <td><span class="badge-teal">{{ $a->score }}/{{ $a->rubric->max_score }}</span></td>
                        <td>{{ \App\Support\GradeScale::letter($pct) }}</td>
                        <td>
                            @if($a->status === 'approved')
                                <span class="verified-badge">Approved</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis">Pending</span>
                            @endif
                        </td>
                        <td>{{ $a->instructor->name }}</td>
                        <td class="small text-muted">{{ $a->semester?->label() ?? '—' }}</td>
                        <td>{{ $a->date->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="11" class="text-muted text-center py-3">No records match this filter.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $assessments->links() }}</div>
    </div>
@endsection
