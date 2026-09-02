@extends('layouts.app')
@section('title', 'My Portfolio')
@section('content')
    <div class="card-tvet mb-4 d-flex flex-row align-items-center gap-3 flex-wrap">
        <div class="avatar-circle" style="width:72px;height:72px;font-size:1.6rem;">{{ strtoupper(substr($student->name,0,1)) }}</div>
        <div class="flex-grow-1">
            <div class="fw-bold fs-5">{{ $student->name }}</div>
            <div class="text-muted small">
                {{ $student->email }}
                @if($student->student_number) &middot; {{ $student->student_number }} @endif
                @if($student->level) &middot; {{ $student->level }} @endif
            </div>
        </div>
        <div class="d-flex gap-4 text-center flex-wrap">
            <div><div class="stat-counter">{{ $stats['tasks_assessed'] }}</div><div class="text-muted small">Tasks Assessed</div></div>
            <div><div class="stat-counter">{{ $stats['average_score_pct'] }}%</div><div class="text-muted small">Average Score</div></div>
            <div><div class="stat-counter">{{ $stats['competencies_verified'] }}</div><div class="text-muted small">Competencies Verified</div></div>
            <div><div class="stat-counter">{{ $report['cgpa'] ?? '—' }}</div><div class="text-muted small">CGPA</div></div>
            @if($stats['pending_count'] > 0)
                <div><div class="stat-counter text-warning">{{ $stats['pending_count'] }}</div><div class="text-muted small">Awaiting Approval</div></div>
            @endif
        </div>
    </div>

    @if(count($report['rows']))
        <div class="card-tvet mb-4">
            <div class="fw-bold mb-3" style="font-size:16px;">Report Card — Course Results</div>
            <div class="table-responsive">
                <table class="table table-tvet align-middle">
                    <thead><tr><th>Course</th><th>Credit Hrs</th><th>Score</th><th>Grade</th><th>Attendance</th><th>Instructor Feedback</th></tr></thead>
                    <tbody>
                    @foreach($report['rows'] as $row)
                        <tr>
                            <td>{{ $row['course']->course_name }}</td>
                            <td>{{ $row['credit_hours'] }}</td>
                            <td>{{ $row['percent'] !== null ? $row['percent'].'%' : '—' }}</td>
                            <td>@if($row['letter'])<span class="badge-teal">{{ $row['letter'] }}</span>@else <span class="text-muted small">Pending</span> @endif</td>
                            <td>{{ $row['attendance'] !== null ? $row['attendance'].'%' : '—' }}</td>
                            <td class="small text-muted">{{ $row['feedback'] ?: '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="small text-muted">CGPA is credit-hour weighted across graded courses. Average attendance: {{ $report['average_attendance'] ?? '—' }}%.</div>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="section-title mb-0">My Competencies</h1>
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('student.portfolio.share') }}">
                @csrf
                <button class="btn btn-sm btn-outline-teal">Generate Public Link</button>
            </form>
            <a href="{{ route('student.portfolio.export') }}" class="btn btn-sm btn-teal"><i class="bi bi-file-earmark-pdf me-1"></i>Export PDF</a>
        </div>
    </div>

    <div class="row g-3">
        @forelse($assessments as $a)
            <div class="col-md-4">
                <div class="card-tvet h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-bold">{{ $a->rubric->criterion }}</div>
                            <div class="text-muted small">{{ $a->rubric->course->course_name }} &middot; {{ $a->rubric->course->trade->trade_name }}</div>
                        </div>
                    </div>
                    <div class="progress mb-2">
                        @php $pct = $a->rubric->max_score > 0 ? min(100, round(($a->score / $a->rubric->max_score) * 100)) : 0; @endphp
                        <div class="progress-bar" style="width: {{ $pct }}%"></div>
                    </div>
                    <div class="small mb-2">Score: <strong>{{ $a->score }}/{{ $a->rubric->max_score }}</strong> ({{ $pct }}%)</div>
                    @if($a->evidence->count())
                        <div class="d-flex flex-wrap gap-1 mb-2">
                            @foreach($a->evidence as $ev)
                                <a href="{{ $ev->url() }}" target="_blank" class="badge bg-light text-dark border">
                                    <i class="bi bi-paperclip"></i> {{ $ev->typeLabel() }} {{ $loop->iteration }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                    <div class="text-muted small mb-2">
                        {{ $a->date->format('d M Y') }}
                        @if($a->semester) &middot; {{ $a->semester->label() }} @endif
                    </div>
                    @if($a->status === 'approved')
                        <span class="verified-badge"><i class="bi bi-check-circle-fill"></i> Verified by {{ $a->instructor->name }}</span>
                    @else
                        <span class="badge bg-warning-subtle text-warning-emphasis"><i class="bi bi-hourglass-split"></i> Awaiting HOD approval</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card-tvet text-center text-muted py-5">
                    No assessments have been recorded yet. Once your instructor scores a practical task, it will appear here automatically.
                </div>
            </div>
        @endforelse
    </div>
@endsection
