@extends('layouts.app')
@section('title', 'Add User')
@section('content')
    <h1 class="section-title mb-4">Add User</h1>
    <div class="card-tvet" style="max-width:640px;">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            @include('admin.users._form', ['user' => null])
            <button class="btn btn-teal mt-2">Create User</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-link">Cancel</a>
        </form>
    </div>
@endsection
