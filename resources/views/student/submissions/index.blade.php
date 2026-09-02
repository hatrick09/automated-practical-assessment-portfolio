@extends('layouts.app')
@section('title', 'My Submissions')
@section('content')
    <h1 class="section-title mb-4">My Submissions</h1>

    <div class="card-tvet mb-4" style="max-width:640px;">
        <div class="fw-bold mb-3" style="font-size:16px;">Upload Work for Review</div>
        @if($courses->isEmpty())
            <p class="text-muted small mb-0">You are not enrolled in any courses yet. Contact an administrator.</p>
        @else
            <form method="POST" action="{{ route('student.submissions.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Course</label>
                    <select name="course_id" class="form-select" required>
                        <option value="">Select course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" @selected(old('course_id')==$course->id)>{{ $course->course_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Task / Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Wiring exercise 3" value="{{ old('title') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Description (optional)</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">What are you submitting?</label>
                    <select name="type" id="submission-type" class="form-select" required onchange="tvetToggleSubmissionType()">
                        <option value="file">A file (image, PDF, Word, ZIP)</option>
                        <option value="video_link">A video link (e.g. YouTube, Drive)</option>
                        <option value="code_link">A code / GitHub link</option>
                    </select>
                </div>
                <div class="mb-3" id="file-field">
                    <label class="form-label small fw-semibold">File</label>
                    <input type="file" name="file" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.zip">
                    <div class="form-text">Images, PDF, Word, or ZIP — max 10MB.</div>
                </div>
                <div class="mb-3" id="url-field" style="display:none;">
                    <label class="form-label small fw-semibold">Link URL</label>
                    <input type="url" name="url" class="form-control" placeholder="https://...">
                </div>
                <button class="btn btn-teal">Submit Work</button>
            </form>
        @endif
    </div>

    <div class="card-tvet">
        <div class="fw-bold mb-3" style="font-size:16px;">Submission History</div>
        <div class="table-responsive">
            <table class="table table-tvet align-middle">
                <thead><tr><th>Title</th><th>Course</th><th>Type</th><th>Link</th><th>Status</th><th>Submitted</th></tr></thead>
                <tbody>
                @forelse($submissions as $s)
                    <tr>
                        <td>{{ $s->title }}</td>
                        <td>{{ $s->course->course_name }}</td>
                        <td class="small text-muted">{{ $s->typeLabel() }}</td>
                        <td><a href="{{ $s->url() }}" target="_blank"><i class="bi bi-paperclip"></i> Open</a></td>
                        <td>
                            @if($s->status === 'reviewed')
                                <span class="verified-badge"><i class="bi bi-check-circle-fill"></i> Reviewed</span>
                            @else
                                <span class="badge bg-light text-dark border">Pending review</span>
                            @endif
                        </td>
                        <td>{{ $s->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted text-center py-3">You haven't submitted any work yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $submissions->links() }}</div>
    </div>
@endsection

@push('scripts')
<script>
    function tvetToggleSubmissionType() {
        const type = document.getElementById('submission-type').value;
        document.getElementById('file-field').style.display = type === 'file' ? 'block' : 'none';
        document.getElementById('url-field').style.display = type === 'file' ? 'none' : 'block';
    }
    tvetToggleSubmissionType();
</script>
@endpush
