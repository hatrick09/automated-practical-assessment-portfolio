@extends('layouts.app')
@section('title', 'Rubrics')
@section('content')
    <h1 class="section-title mb-4">Rubrics</h1>

    <div class="card-tvet mb-4">
        <form method="POST" action="{{ route('admin.rubrics.store') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-4">
                <label class="form-label small">Course</label>
                <select name="course_id" class="form-select" required>
                    <option value="">Select course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label small">Criterion</label>
                <input type="text" name="criterion" class="form-control" placeholder="e.g. Safe use of hand tools" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Max Score</label>
                <input type="number" name="max_score" class="form-control" min="1" max="1000" required>
            </div>
            <div class="col-md-1"><button class="btn btn-teal w-100">Add</button></div>
        </form>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-auto">
            <select name="course_id" class="form-select" onchange="this.form.submit()">
                <option value="">All courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>{{ $course->course_name }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="card-tvet">
        <table class="table table-tvet align-middle">
            <thead><tr><th>Course</th><th>Criterion</th><th>Max Score</th><th></th></tr></thead>
            <tbody>
            @forelse($rubrics as $rubric)
                <tr>
                    <td>{{ $rubric->course->course_name }} <span class="text-muted small">({{ $rubric->course->trade->trade_name }})</span></td>
                    <td>{{ $rubric->criterion }}</td>
                    <td>{{ $rubric->max_score }}</td>
                    <td class="text-end">
                        <form action="{{ route('admin.rubrics.destroy', $rubric) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete rubric?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-muted text-center py-3">No rubrics yet.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div>{{ $rubrics->links() }}</div>
    </div>
@endsection
