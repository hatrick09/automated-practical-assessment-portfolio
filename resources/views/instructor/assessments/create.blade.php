@extends('layouts.app')
@section('title', 'Score: ' . $course->course_name)
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="section-title mb-0">{{ $course->course_name }}</h1>
        <a href="{{ route('instructor.assessments.index') }}" class="btn btn-link">&larr; Back to courses</a>
    </div>

    <div class="card-tvet mb-4" style="max-width:420px;">
        <label class="form-label small fw-semibold">Select Student</label>
        <select id="studentSelect" class="form-select" onchange="location = this.value">
            <option value="{{ route('instructor.assessments.create', $course) }}">-- choose a student --</option>
            @foreach($students as $student)
                <option value="{{ route('instructor.assessments.create', $course) }}?student_id={{ $student->id }}" @selected($selectedStudent && $selectedStudent->id === $student->id)>{{ $student->name }}</option>
            @endforeach
        </select>
    </div>

    @if($selectedStudent)
        <form method="POST" action="{{ route('instructor.assessments.store', $course) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">

            <div class="card-tvet mb-3 d-flex flex-row align-items-center gap-3">
                <div class="avatar-circle">{{ strtoupper(substr($selectedStudent->name,0,1)) }}</div>
                <div>
                    <div class="fw-bold">{{ $selectedStudent->name }}</div>
                    <div class="text-muted small">{{ $selectedStudent->email }}</div>
                    @if($currentSemester)
                        <div class="text-muted small">Recording for: <strong>{{ $currentSemester->label() }}</strong></div>
                    @else
                        <div class="text-muted small text-warning">No current semester set — ask an admin to set one under Academic Years.</div>
                    @endif
                </div>
                <div class="ms-auto">
                    <label class="form-label small mb-0">Date</label>
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="alert alert-info py-2 small">
                Submitted scores go to your Head of Department for approval before they appear on the student's official portfolio.
            </div>

            @php $studentSubmissions = $course->submissions()->where('student_id', $selectedStudent->id)->latest()->get(); @endphp
            @if($studentSubmissions->isNotEmpty())
                <div class="card-tvet mb-3">
                    <div class="fw-bold mb-2" style="font-size:15px;">Work Submitted by This Student</div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($studentSubmissions as $sub)
                            <a href="{{ $sub->url() }}" target="_blank" class="badge bg-light text-dark border p-2">
                                <i class="bi bi-paperclip"></i> {{ $sub->title }} ({{ $sub->typeLabel() }})
                                @if($sub->status === 'pending')<span class="text-danger">&middot; pending</span>@endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="card-tvet">
                <div class="table-responsive">
                    <table class="table table-tvet align-middle">
                        <thead>
                            <tr>
                                <th style="min-width:220px;">Criterion</th>
                                <th style="width:110px;">Max Score</th>
                                <th style="width:120px;">Score</th>
                                <th style="min-width:180px;">Remarks</th>
                                <th style="min-width:260px;">Evidence</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($rubrics as $rubric)
                            <tr>
                                <td class="fw-semibold">{{ $rubric->criterion }}</td>
                                <td><span class="badge-teal">{{ $rubric->max_score }}</span></td>
                                <td>
                                    <input type="number" min="0" max="{{ $rubric->max_score }}" class="form-control form-control-sm score-input"
                                           name="scores[{{ $rubric->id }}]" data-max="{{ $rubric->max_score }}"
                                           value="{{ old('scores.'.$rubric->id) }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="remarks[{{ $rubric->id }}]" value="{{ old('remarks.'.$rubric->id) }}" placeholder="Optional">
                                </td>
                                <td>
                                    <input type="file" class="form-control form-control-sm mb-1" name="evidence[{{ $rubric->id }}][]" multiple accept=".jpg,.jpeg,.png,.pdf">
                                    <div class="d-flex gap-1 mb-1">
                                        <select class="form-select form-select-sm evidence-type" name="evidence_type[{{ $rubric->id }}]" data-target="link-{{ $rubric->id }}">
                                            <option value="">+ Add link (optional)</option>
                                            <option value="video_link">Video link</option>
                                            <option value="code_link">Code / GitHub link</option>
                                        </select>
                                    </div>
                                    <input type="url" id="link-{{ $rubric->id }}" class="form-control form-control-sm mb-1" name="evidence_url[{{ $rubric->id }}]" placeholder="https://..." style="display:none;">
                                    <input type="text" class="form-control form-control-sm" name="evidence_description[{{ $rubric->id }}]" placeholder="Evidence description (optional)">
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted text-center py-3">No rubric criteria defined for this course yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-2">
                    <div>Running Total: <span class="fw-bold" id="runningTotal">0</span> / <span id="maxTotal">0</span></div>
                    <button type="submit" class="btn btn-teal px-4">Submit Assessment</button>
                </div>
            </div>
        </form>
    @else
        <p class="text-muted">Choose a student above to begin scoring.</p>
    @endif
@endsection

@push('scripts')
<script>
    const scoreInputs = document.querySelectorAll('.score-input');
    function recalc() {
        let total = 0, max = 0;
        scoreInputs.forEach(input => {
            total += parseFloat(input.value) || 0;
            max += parseFloat(input.dataset.max) || 0;
        });
        document.getElementById('runningTotal').textContent = total;
        document.getElementById('maxTotal').textContent = max;
    }
    scoreInputs.forEach(input => input.addEventListener('input', recalc));
    recalc();

    document.querySelectorAll('.evidence-type').forEach(select => {
        select.addEventListener('change', function () {
            const target = document.getElementById(this.dataset.target);
            target.style.display = this.value ? 'block' : 'none';
            if (!this.value) target.value = '';
        });
    });
</script>
@endpush
