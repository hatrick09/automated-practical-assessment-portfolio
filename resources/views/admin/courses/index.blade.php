@extends('layouts.app')
@section('title', 'Courses')
@section('content')
    <h1 class="section-title mb-4">Courses</h1>

    <div class="card-tvet mb-4" style="max-width:760px;">
        <form method="POST" action="{{ route('admin.courses.store') }}" class="row g-2">
            @csrf
            <div class="col-3">
                <select name="trade_id" class="form-select" required>
                    <option value="">Select trade</option>
                    @foreach($trades as $trade)
                        <option value="{{ $trade->id }}">{{ $trade->trade_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-3"><input type="text" name="course_name" class="form-control" placeholder="New course name" required></div>
            <div class="col-2"><input type="text" name="course_code" class="form-control" placeholder="Code (e.g. EE201)"></div>
            <div class="col-2"><input type="number" name="credit_hours" class="form-control" placeholder="Credit hrs" min="1" max="20" value="3"></div>
            <div class="col-auto"><button class="btn btn-teal">Add</button></div>
        </form>
    </div>

    <div class="card-tvet">
        <table class="table table-tvet align-middle">
            <thead><tr><th>Code</th><th>Course</th><th>Trade</th><th>Credit Hrs</th><th>Rubrics</th><th></th></tr></thead>
            <tbody>
            @forelse($courses as $course)
                <tr>
                    <td class="text-muted small">{{ $course->course_code ?: '—' }}</td>
                    <td>{{ $course->course_name }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.courses.update', $course) }}" class="d-flex gap-2">
                            @csrf @method('PUT')
                            <select name="trade_id" class="form-select form-select-sm">
                                @foreach($trades as $trade)
                                    <option value="{{ $trade->id }}" @selected($trade->id === $course->trade_id)>{{ $trade->trade_name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="course_name" value="{{ $course->course_name }}">
                            <input type="hidden" name="course_code" value="{{ $course->course_code }}">
                            <input type="hidden" name="credit_hours" value="{{ $course->credit_hours }}">
                            <button class="btn btn-sm btn-outline-teal">Save</button>
                        </form>
                    </td>
                    <td>{{ $course->credit_hours }}</td>
                    <td>{{ $course->rubrics_count }}</td>
                    <td class="text-end">
                        <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Delete course?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted text-center py-3">No courses yet.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div>{{ $courses->links() }}</div>
    </div>
@endsection
