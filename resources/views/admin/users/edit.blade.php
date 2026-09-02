@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
    <h1 class="section-title mb-4">Edit User</h1>
    <div class="card-tvet" style="max-width:640px;">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')
            @include('admin.users._form', ['user' => $user])
            <button class="btn btn-teal mt-2">Update User</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-link">Cancel</a>
        </form>
    </div>
@endsection
