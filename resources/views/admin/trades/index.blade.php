@extends('layouts.app')
@section('title', 'Trades')
@section('content')
    <h1 class="section-title mb-4">Trades</h1>

    <div class="card-tvet mb-4" style="max-width:640px;">
        <form method="POST" action="{{ route('admin.trades.store') }}" class="row g-2">
            @csrf
            <div class="col"><input type="text" name="trade_name" class="form-control" placeholder="New trade name" required></div>
            <div class="col-4">
                <select name="programme_id" class="form-select">
                    <option value="">-- no programme --</option>
                    @foreach($programmes as $programme)
                        <option value="{{ $programme->id }}">{{ $programme->name }} ({{ $programme->department->name ?? '' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-teal">Add</button></div>
        </form>
        <div class="form-text mt-2">Linking a trade to a programme lets it roll up into department-level reports and the HOD dashboard.</div>
    </div>

    <div class="card-tvet">
        <table class="table table-tvet align-middle">
            <thead><tr><th>Trade</th><th>Programme</th><th>Courses</th><th></th></tr></thead>
            <tbody>
            @forelse($trades as $trade)
                <tr>
                    <td>
                        <form method="POST" action="{{ route('admin.trades.update', $trade) }}" class="d-flex gap-2">
                            @csrf @method('PUT')
                            <input type="text" name="trade_name" value="{{ $trade->trade_name }}" class="form-control form-control-sm">
                            <select name="programme_id" class="form-select form-select-sm">
                                <option value="">-- none --</option>
                                @foreach($programmes as $programme)
                                    <option value="{{ $programme->id }}" @selected($programme->id === $trade->programme_id)>{{ $programme->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-sm btn-outline-teal">Save</button>
                        </form>
                    </td>
                    <td class="text-muted small">{{ $trade->programme->department->name ?? '—' }}</td>
                    <td>{{ $trade->courses_count }}</td>
                    <td class="text-end">
                        <form action="{{ route('admin.trades.destroy', $trade) }}" method="POST" onsubmit="return confirm('Delete trade?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-muted text-center py-3">No trades yet.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div>{{ $trades->links() }}</div>
    </div>
@endsection
