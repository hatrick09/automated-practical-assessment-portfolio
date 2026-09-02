@extends('layouts.app')
@section('title', 'Users')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="section-title mb-0">Users</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-teal"><i class="bi bi-plus-lg me-1"></i>Add User</a>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-auto">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name or email">
        </div>
        <div class="col-auto">
            <select name="role" class="form-select">
                <option value="">All roles</option>
                @foreach(['admin','instructor','student'] as $r)
                    <option value="{{ $r }}" @selected(request('role')===$r)>{{ ucfirst($r) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-outline-teal">Filter</button></div>
    </form>

    <div class="card-tvet">
        <div class="table-responsive">
            <table class="table table-tvet align-middle">
                <thead><tr><th>Name</th><th>Email</th><th>Role</th><th></th></tr></thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge-teal">{{ ucfirst($user->role) }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-teal">Edit</a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this user?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted text-center py-3">No users found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-2">{{ $users->links() }}</div>
    </div>
@endsection
