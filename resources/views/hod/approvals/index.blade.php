@extends('layouts.app')
@section('title', 'Assessment Approvals')
@section('content')
    <h1 class="section-title mb-4">Assessment Approvals</h1>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-auto">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="pending" @selected($status=='pending')>Pending</option>
                <option value="approved" @selected($status=='approved')>Approved</option>
                <option value="all" @selected($status=='all')>All</option>
            </select>
        </div>
    </form>

    <div class="card-tvet">
        <table class="table table-tvet align-middle">
            <thead><tr><th>Student</th><th>Course</th><th>Criterion</th><th>Score</th><th>Instructor</th><th>Date</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($assessments as $a)
                <tr>
                    <td>{{ $a->student->name }}</td>
                    <td>{{ $a->rubric->course->course_name }}</td>
                    <td>{{ $a->rubric->criterion }}</td>
                    <td><span class="badge-teal">{{ $a->score }}/{{ $a->rubric->max_score }}</span></td>
                    <td>{{ $a->instructor->name }}</td>
                    <td>{{ $a->date->format('d M Y') }}</td>
                    <td>
                        @if($a->status === 'approved')
                            <span class="verified-badge">Approved</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis">Pending</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if($a->status !== 'approved')
                            <form method="POST" action="{{ route('hod.approvals.approve', $a) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-teal">Approve</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-muted text-center py-3">Nothing here.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div>{{ $assessments->links() }}</div>
    </div>
@endsection
