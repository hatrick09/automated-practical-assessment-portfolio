<div class="mb-3">
    <label class="form-label small fw-semibold">Full Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label small fw-semibold">Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label small fw-semibold">Password {{ $user ? '(leave blank to keep current)' : '' }}</label>
    <input type="password" name="password" class="form-control" {{ $user ? '' : 'required' }}>
</div>
<div class="mb-3">
    <label class="form-label small fw-semibold">Role</label>
    <select name="role" id="role-select" class="form-select" required onchange="tvetToggleRoleFields()">
        @foreach(['admin','instructor','student'] as $r)
            <option value="{{ $r }}" @selected(old('role', $user->role ?? '')===$r)>{{ ucfirst($r) }}</option>
        @endforeach
    </select>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label small fw-semibold">Department</label>
        <select name="department_id" class="form-select">
            <option value="">-- none --</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}" @selected(old('department_id', $user->department_id ?? '') == $department->id)>{{ $department->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 mb-3" id="hod-field">
        <label class="form-label small fw-semibold d-block">Head of Department?</label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="is_hod" value="1" id="is_hod" @checked(old('is_hod', $user->is_hod ?? false))>
            <label class="form-check-label small" for="is_hod">Grant HOD access (approvals, department dashboard)</label>
        </div>
    </div>
</div>

<div class="row" id="student-fields">
    <div class="col-md-4 mb-3">
        <label class="form-label small fw-semibold">Student Number</label>
        <input type="text" name="student_number" class="form-control" value="{{ old('student_number', $user->student_number ?? '') }}">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label small fw-semibold">Level</label>
        <input type="text" name="level" class="form-control" placeholder="e.g. Level 200" value="{{ old('level', $user->level ?? '') }}">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label small fw-semibold">Gender</label>
        <select name="gender" class="form-select">
            <option value="">-- unspecified --</option>
            @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $val => $label)
                <option value="{{ $val }}" @selected(old('gender', $user->gender ?? '')===$val)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mb-3" id="courses-field">
    <label class="form-label small fw-semibold">Courses (assign for instructor/student)</label>
    <select name="courses[]" class="form-select" multiple size="6">
        @php $assigned = $assignedCourseIds ?? []; @endphp
        @foreach($courses as $course)
            <option value="{{ $course->id }}" @selected(in_array($course->id, $assigned))>{{ $course->course_name }} ({{ $course->trade->trade_name ?? '' }})</option>
        @endforeach
    </select>
    <div class="form-text">Hold Ctrl/Cmd to select multiple.</div>
</div>

@push('scripts')
<script>
    function tvetToggleRoleFields() {
        const role = document.getElementById('role-select').value;
        document.getElementById('hod-field').style.display = role === 'instructor' ? 'block' : 'none';
        document.getElementById('student-fields').style.display = role === 'student' ? 'flex' : 'none';
    }
    tvetToggleRoleFields();
</script>
@endpush
