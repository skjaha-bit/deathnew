@extends('admin::layouts.app')

@section('title', 'Add Admin User')

@section('content')
    @include('admin::components.page-header', ['title' => 'Add Admin User', 'subtitle' => 'They will receive an email to set their own password.'])

    <div class="card p-4" style="max-width: 480px;">
        <form method="POST" action="{{ route('admin.admin-users.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    @foreach ($assignableRoles as $role)
                        <option value="{{ $role }}" @selected(old('role') === $role)>{{ ucwords(str_replace('-', ' ', $role)) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Create admin user</button>
            <a href="{{ route('admin.admin-users.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
@endsection
