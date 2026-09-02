@extends('layouts.app')
@section('title', 'Departments')
@section('content')
    <h1 class="section-title mb-4">Departments</h1>

    <div class="card-tvet mb-4" style="max-width:520px;">
        <form method="POST" action="{{ route('admin.departments.store') }}" class="row g-2">
            @csrf
            <div class="col"><input type="text" name="name" class="form-control" placeholder="New department name (e.g. Engineering)" required></div>
            <div class="col-auto"><button class="btn btn-teal">Add</button></div>
        </form>
    </div>

    <div class="card-tvet">
        <table class="table table-tvet align-middle">
            <thead><tr><th>Department</th><th>Programmes</th><th>Staff/Students</th><th></th></tr></thead>
            <tbody>
            @forelse($departments as $department)
                <tr>
                    <td>
                        <form method="POST" action="{{ route('admin.departments.update', $department) }}" class="d-flex gap-2">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $department->name }}" class="form-control form-control-sm">
                            <button class="btn btn-sm btn-outline-teal">Save</button>
                        </form>
                    </td>
                    <td>{{ $department->programmes_count }}</td>
                    <td>{{ $department->users_count }}</td>
                    <td class="text-end">
                        <form action="{{ route('admin.departments.destroy', $department) }}" method="POST" onsubmit="return confirm('Delete department?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-muted text-center py-3">No departments yet.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div>{{ $departments->links() }}</div>
    </div>
@endsection
