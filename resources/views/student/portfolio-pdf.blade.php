<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#222; }
        h1 { color:#1B3A4B; font-size:20px; margin-bottom:0; }
        h2 { color:#1B3A4B; font-size:14px; margin-top:22px; margin-bottom:6px; border-bottom: 2px solid #E8F3F0; padding-bottom:4px; }
        .muted { color:#666; }
        table { width:100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #DCE3E1; padding: 6px 8px; text-align: left; font-size:11px; }
        th { background:#E8F3F0; color:#2E7D6B; }
        .badge { background:#E8F3F0; color:#2E7D6B; padding:2px 6px; border-radius:4px; font-weight:bold; }
        .header-row { width:100%; }
        .header-row td { border: none; padding: 0; vertical-align: top; }
        .qr-box { text-align:right; }
        .stat-strip td { border:none; padding:4px 12px 4px 0; }
        .stat-value { font-size:16px; font-weight:bold; color:#1B3A4B; }
        .sig-block { margin-top: 40px; }
        .sig-line { border-top: 1px solid #333; width: 220px; margin-top: 36px; padding-top: 4px; font-size: 10px; }
        .footer-note { margin-top: 24px; font-size: 9px; color: #888; }
    </style>
</head>
<body>
    <table class="header-row">
        <tr>
            <td style="width:70%;">
                <h1>{{ $student->name }} — E-Portfolio &amp; Report Card</h1>
                <p class="muted">
                    {{ $student->email }}
                    @if($student->student_number) &middot; Student No: {{ $student->student_number }} @endif
                    @if($student->level) &middot; {{ $student->level }} @endif
                </p>
                <p class="muted">Generated {{ now()->format('d M Y, H:i') }}</p>
            </td>
            <td class="qr-box" style="width:30%;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data={{ urlencode($qrUrl) }}" width="110" height="110" alt="QR code to online portfolio">
                <div class="muted" style="font-size:9px;">Scan to view live portfolio</div>
            </td>
        </tr>
    </table>

    <table class="stat-strip">
        <tr>
            <td><div class="stat-value">{{ $stats['tasks_assessed'] }}</div><div class="muted">Tasks Assessed</div></td>
            <td><div class="stat-value">{{ $stats['average_score_pct'] }}%</div><div class="muted">Average Score</div></td>
            <td><div class="stat-value">{{ $report['cgpa'] ?? '—' }}</div><div class="muted">CGPA</div></td>
            <td><div class="stat-value">{{ $report['average_attendance'] ?? '—' }}%</div><div class="muted">Avg. Attendance</div></td>
        </tr>
    </table>

    <h2>Course Results</h2>
    <table>
        <thead>
            <tr><th>Course</th><th>Credit Hrs</th><th>Score</th><th>Grade</th><th>Attendance</th><th>Instructor Feedback</th></tr>
        </thead>
        <tbody>
        @forelse($report['rows'] as $row)
            <tr>
                <td>{{ $row['course']->course_name }}</td>
                <td>{{ $row['credit_hours'] }}</td>
                <td>{{ $row['percent'] !== null ? $row['percent'].'%' : '—' }}</td>
                <td>{{ $row['letter'] ?? 'Pending' }}</td>
                <td>{{ $row['attendance'] !== null ? $row['attendance'].'%' : '—' }}</td>
                <td>{{ $row['feedback'] ?: '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="6">No enrolled courses.</td></tr>
        @endforelse
        </tbody>
    </table>

    <h2>Practical Assessment Breakdown</h2>
    <table>
        <thead>
            <tr><th>Criterion</th><th>Course</th><th>Trade</th><th>Score</th><th>Status</th><th>Instructor</th><th>Semester</th><th>Date</th></tr>
        </thead>
        <tbody>
        @forelse($assessments as $a)
            <tr>
                <td>{{ $a->rubric->criterion }}</td>
                <td>{{ $a->rubric->course->course_name }}</td>
                <td>{{ $a->rubric->course->trade->trade_name }}</td>
                <td><span class="badge">{{ $a->score }}/{{ $a->rubric->max_score }}</span></td>
                <td>{{ $a->status === 'approved' ? 'Verified' : 'Pending Approval' }}</td>
                <td>{{ $a->instructor->name }}</td>
                <td>{{ $a->semester?->label() ?? '—' }}</td>
                <td>{{ $a->date->format('d M Y') }}</td>
            </tr>
        @empty
            <tr><td colspan="8">No assessments recorded.</td></tr>
        @endforelse
        </tbody>
    </table>

    <h2>Portfolio Evidence</h2>
    <table>
        <thead><tr><th>Task</th><th>Type</th><th>Description</th></tr></thead>
        <tbody>
        @php $evidenceRows = $assessments->flatMap(fn($a) => $a->evidence->map(fn($e) => ['task' => $a->rubric->criterion, 'evidence' => $e])); @endphp
        @forelse($evidenceRows as $row)
            <tr>
                <td>{{ $row['task'] }}</td>
                <td>{{ $row['evidence']->typeLabel() }}</td>
                <td>{{ $row['evidence']->description ?: '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="3">No evidence attached.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="sig-block">
        <table style="width:100%;">
            <tr>
                <td style="width:50%; border:none;">
                    <div class="sig-line">Instructor / HOD Signature</div>
                </td>
                <td style="width:50%; border:none;">
                    <div class="sig-line">Date</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-note">
        Verification status: this document reflects assessments approved as of {{ now()->format('d M Y') }}.
        Digitally generated by the TVET Automated Practical Assessment &amp; E-Portfolio System — not a substitute for a wet-ink signature unless countersigned.
    </div>
</body>
</html>
