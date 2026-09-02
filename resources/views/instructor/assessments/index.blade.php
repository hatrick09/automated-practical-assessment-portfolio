@extends('layouts.app')
@section('title', 'Score Assessments')
@section('content')
    <h1 class="section-title mb-4">Select a Course to Score</h1>
    <div class="row g-3">
        @forelse($courses as $course)
            <div class="col-md-4">
                <div class="card-tvet h-100">
                    <div class="fw-bold mb-1">{{ $course->course_name }}</div>
                    <div class="text-muted small mb-3">{{ $course->students_count }} students</div>
                    <a href="{{ route('instructor.assessments.create', $course) }}" class="btn btn-sm btn-teal">Open Rubric</a>
                </div>
            </div>
        @empty
            <div class="col-12"><p class="text-muted">No courses assigned to you.</p></div>
        @endforelse
    </div>
@endsection
