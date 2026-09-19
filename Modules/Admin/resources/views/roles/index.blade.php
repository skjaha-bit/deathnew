@extends('admin::layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card p-3">
                <h6 class="mb-3">New Role</h6>
                <form method="POST" action="{{ route('admin.roles.store') }}" class="d-flex gap-2">
                    @csrf
                    <input type="text" name="name" class="form-control" placeholder="role-name" required>
                    <button class="btn btn-primary text-nowrap">Add Role</button>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3">
                <h6 class="mb-3">New Permission</h6>
                <form method="POST" action="{{ route('admin.permissions.store') }}" class="d-flex gap-2">
                    @csrf
                    <input type="text" name="name" class="form-control" placeholder="permission-name" required>
                    <button class="btn btn-primary text-nowrap">Add Permission</button>
                </form>
            </div>
        </div>
    </div>

    @foreach ($roles as $role)
        <div class="card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">{{ $role->name }}</h6>
                @if ($role->name !== 'admin')
                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Delete this role?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                @endif
            </div>
            <form method="POST" action="{{ route('admin.roles.sync-permissions', $role) }}">
                @csrf @method('PUT')
                <div class="row row-cols-2 row-cols-md-4 g-2 mb-2">
                    @foreach ($permissions as $permission)
                        <div class="form-check col">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm-{{ $role->id }}-{{ $permission->id }}" @checked($role->permissions->contains('id', $permission->id))>
                            <label class="form-check-label" for="perm-{{ $role->id }}-{{ $permission->id }}">{{ $permission->name }}</label>
                        </div>
                    @endforeach
                </div>
                <button class="btn btn-sm btn-primary">Save Permissions</button>
            </form>
        </div>
    @endforeach
@endsection
