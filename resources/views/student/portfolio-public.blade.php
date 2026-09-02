<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $student->name }} - Public E-Portfolio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family:'Segoe UI',system-ui,sans-serif; background:#F7F9F8; padding:2rem; }
        .card-tvet { background:#fff; border:1px solid #DCE3E1; border-radius:10px; padding:1.25rem; }
        .progress { background:#DCE3E1; border-radius:6px; height:8px; }
        .progress-bar { background:#2E7D6B; }
        .verified-badge { background:#E8F3F0; color:#2E7D6B; font-size:.75rem; font-weight:600; border-radius:20px; padding:.25em .75em; }
        .stat-counter { font-size: 1.4rem; font-weight: 700; color: #1B3A4B; }
    </style>
</head>
<body>
<div class="container" style="max-width:960px;">
    <div class="card-tvet mb-4 d-flex flex-row align-items-center gap-4 flex-wrap">
        <div>
            <h3 class="fw-bold mb-1">{{ $student->name }}</h3>
            <p class="text-muted mb-0">Public E-Portfolio &middot; TVET Institution</p>
        </div>
        <div class="d-flex gap-4 text-center ms-auto">
            <div><div class="stat-counter">{{ $assessments->count() }}</div><div class="text-muted small">Verified Tasks</div></div>
            <div><div class="stat-counter">{{ $report['cgpa'] ?? '—' }}</div><div class="text-muted small">CGPA</div></div>
        </div>
    </div>

    @if(count($report['rows']))
        <div class="card-tvet mb-4">
            <div class="fw-bold mb-3">Course Results</div>
            <table class="table">
                <thead><tr><th>Course</th><th>Grade</th></tr></thead>
                <tbody>
                @foreach($report['rows'] as $row)
                    <tr>
                        <td>{{ $row['course']->course_name }}</td>
                        <td>{{ $row['letter'] ?? 'Pending' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="row g-3">
        @foreach($assessments as $a)
            <div class="col-md-4">
                <div class="card-tvet h-100">
                    <div class="fw-bold">{{ $a->rubric->criterion }}</div>
                    <div class="text-muted small mb-2">{{ $a->rubric->course->course_name }}</div>
                    @php $pct = $a->rubric->max_score > 0 ? min(100, round(($a->score / $a->rubric->max_score) * 100)) : 0; @endphp
                    <div class="progress mb-2"><div class="progress-bar" style="width:{{ $pct }}%"></div></div>
                    <div class="small mb-2">{{ $a->score }}/{{ $a->rubric->max_score }} ({{ $pct }}%)</div>
                    <span class="verified-badge">Verified by {{ $a->instructor->name }}</span>
                </div>
            </div>
        @endforeach
    </div>
</div>
</body>
</html>
