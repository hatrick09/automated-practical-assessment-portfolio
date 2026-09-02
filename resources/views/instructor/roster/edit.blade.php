@extends('layouts.app')
@section('title', 'Attendance & Feedback: ' . $course->course_name)
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="section-title mb-0">{{ $course->course_name }} — Attendance &amp; Feedback</h1>
        <a href="{{ route('instructor.assessments.index') }}" class="btn btn-link">&larr; Back to courses</a>
    </div>

    <form method="POST" action="{{ route('instructor.roster.update', $course) }}">
        @csrf @method('PUT')
        <div class="card-tvet">
            <div class="table-responsive">
                <table class="table table-tvet align-middle">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th style="width:160px;">Attendance %</th>
                            <th>Overall Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $student->name }}</td>
                            <td>
                                <input type="number" min="0" max="100" step="0.1" class="form-control form-control-sm"
                                       name="attendance[{{ $student->id }}]" value="{{ $student->pivot->attendance_percentage }}">
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm"
                                       name="feedback[{{ $student->id }}]" value="{{ $student->pivot->overall_feedback }}"
                                       placeholder="e.g. Demonstrates excellent practical ability but should improve documentation.">
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-muted text-center py-3">No students enrolled in this course yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <button class="btn btn-teal mt-2">Save</button>
        </div>
    </form>
@endsection
