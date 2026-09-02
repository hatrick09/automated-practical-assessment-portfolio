@extends('layouts.app')
@section('title', 'Academic Years & Semesters')
@section('content')
    <h1 class="section-title mb-4">Academic Years &amp; Semesters</h1>

    <div class="card-tvet mb-4" style="max-width:420px;">
        <form method="POST" action="{{ route('admin.academic.years.store') }}" class="row g-2">
            @csrf
            <div class="col"><input type="text" name="name" class="form-control" placeholder="e.g. 2025/2026" required></div>
            <div class="col-auto"><button class="btn btn-teal">Add Year</button></div>
        </form>
    </div>

    @forelse($academicYears as $year)
        <div class="card-tvet mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-bold">
                    {{ $year->name }}
                    @if($year->is_current)<span class="verified-badge ms-2">Current</span>@endif
                </div>
                <div class="d-flex gap-2">
                    @unless($year->is_current)
                        <form method="POST" action="{{ route('admin.academic.years.setCurrent', $year) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-outline-teal">Set as Current</button>
                        </form>
                    @endunless
                    <form method="POST" action="{{ route('admin.academic.years.destroy', $year) }}" onsubmit="return confirm('Delete this academic year and its semesters?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </div>
            </div>

            <table class="table table-tvet align-middle mb-3">
                <thead><tr><th>Semester</th><th></th><th></th></tr></thead>
                <tbody>
                @forelse($year->semesters as $semester)
                    <tr>
                        <td>{{ $semester->name }}</td>
                        <td>@if($semester->is_current)<span class="verified-badge">Current</span>@endif</td>
                        <td class="text-end">
                            @unless($semester->is_current)
                                <form method="POST" action="{{ route('admin.academic.semesters.setCurrent', $semester) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-teal">Set Current</button>
                                </form>
                            @endunless
                            <form method="POST" action="{{ route('admin.academic.semesters.destroy', $semester) }}" class="d-inline" onsubmit="return confirm('Delete semester?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-muted small">No semesters yet.</td></tr>
                @endforelse
                </tbody>
            </table>

            <form method="POST" action="{{ route('admin.academic.semesters.store', $year) }}" class="row g-2">
                @csrf
                <div class="col-auto"><input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Semester 1" required></div>
                <div class="col-auto"><button class="btn btn-sm btn-outline-teal">Add Semester</button></div>
            </form>
        </div>
    @empty
        <div class="card-tvet text-muted text-center py-4">No academic years yet. Add one above.</div>
    @endforelse
@endsection
