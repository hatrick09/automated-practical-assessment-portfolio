@extends('layouts.app')
@section('title', 'Student Submissions')
@section('content')
    <h1 class="section-title mb-4">Student Submissions</h1>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-auto">
            <select name="course_id" class="form-select" onchange="this.form.submit()">
                <option value="">All courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @selected(request('course_id')==$course->id)>{{ $course->course_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">All statuses</option>
                <option value="pending" @selected(request('status')==='pending')>Pending review</option>
                <option value="reviewed" @selected(request('status')==='reviewed')>Reviewed</option>
            </select>
        </div>
    </form>

    <div class="card-tvet">
        <div class="table-responsive">
            <table class="table table-tvet align-middle">
                <thead><tr><th>Student</th><th>Course</th><th>Title</th><th>File</th><th>Status</th><th>Submitted</th><th></th></tr></thead>
                <tbody>
                @forelse($submissions as $s)
                    <tr>
                        <td>{{ $s->student->name }}</td>
                        <td>{{ $s->course->course_name }}</td>
                        <td>{{ $s->title }}@if($s->description)<div class="text-muted small">{{ $s->description }}</div>@endif</td>
                        <td><a href="{{ $s->url() }}" target="_blank"><i class="bi bi-paperclip"></i> View ({{ $s->typeLabel() }})</a></td>
                        <td>
                            @if($s->status === 'reviewed')
                                <span class="verified-badge"><i class="bi bi-check-circle-fill"></i> Reviewed</span>
                            @else
                                <span class="badge bg-light text-dark border">Pending</span>
                            @endif
                        </td>
                        <td>{{ $s->created_at->format('d M Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('instructor.assessments.create', $s->course) }}?student_id={{ $s->student_id }}" class="btn btn-sm btn-teal">Score This Student</a>
                            @if($s->status !== 'reviewed')
                                <form action="{{ route('instructor.submissions.review', $s) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-teal">Mark Reviewed</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-muted text-center py-3">No submissions from your students yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $submissions->links() }}</div>
    </div>
@endsection
