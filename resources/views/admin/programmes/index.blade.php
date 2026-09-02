@extends('layouts.app')
@section('title', 'Programmes')
@section('content')
    <h1 class="section-title mb-4">Programmes</h1>

    <div class="card-tvet mb-4" style="max-width:640px;">
        <form method="POST" action="{{ route('admin.programmes.store') }}" class="row g-2">
            @csrf
            <div class="col-4">
                <select name="department_id" class="form-select" required>
                    <option value="">Select department</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col"><input type="text" name="name" class="form-control" placeholder="New programme name (e.g. Mechanical Engineering)" required></div>
            <div class="col-auto"><button class="btn btn-teal">Add</button></div>
        </form>
    </div>

    <div class="card-tvet">
        <table class="table table-tvet align-middle">
            <thead><tr><th>Programme</th><th>Department</th><th>Trades linked</th><th></th></tr></thead>
            <tbody>
            @forelse($programmes as $programme)
                <tr>
                    <td>{{ $programme->name }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.programmes.update', $programme) }}" class="d-flex gap-2">
                            @csrf @method('PUT')
                            <select name="department_id" class="form-select form-select-sm">
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" @selected($department->id === $programme->department_id)>{{ $department->name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="name" value="{{ $programme->name }}">
                            <button class="btn btn-sm btn-outline-teal">Save</button>
                        </form>
                    </td>
                    <td>{{ $programme->trades_count }}</td>
                    <td class="text-end">
                        <form action="{{ route('admin.programmes.destroy', $programme) }}" method="POST" onsubmit="return confirm('Delete programme?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-muted text-center py-3">No programmes yet. Add a department first.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div>{{ $programmes->links() }}</div>
    </div>
@endsection
