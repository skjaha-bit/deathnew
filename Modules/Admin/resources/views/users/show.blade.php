@extends('admin::layouts.app')

@section('title', 'User: '.$user->name)

@section('content')
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card p-3">
                <h6 class="mb-3">Profile</h6>
                <dl class="row mb-0">
                    <dt class="col-4">Name</dt><dd class="col-8">{{ $user->name }}</dd>
                    <dt class="col-4">Email</dt><dd class="col-8">{{ $user->email }}</dd>
                    <dt class="col-4">Phone</dt><dd class="col-8">{{ $user->phone_code }} {{ $user->phone_number }}</dd>
                    <dt class="col-4">Kennitala</dt><dd class="col-8">{{ $user->kennitala ?? '—' }}</dd>
                    <dt class="col-4">Registered</dt><dd class="col-8">{{ $user->created_at }}</dd>
                </dl>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-3 mb-3">
                <h6 class="mb-3">Status</h6>
                <form method="POST" action="{{ route('admin.users.update-status', $user) }}" class="d-flex gap-2">
                    @csrf @method('PUT')
                    <select name="status" class="form-select">
                        @foreach (['active', 'suspended', 'deceased'] as $status)
                            <option value="{{ $status }}" @selected($user->status === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-primary text-nowrap">Update</button>
                </form>
            </div>

            <div class="card p-3">
                <h6 class="mb-3">Roles</h6>
                <form method="POST" action="{{ route('admin.users.sync-roles', $user) }}">
                    @csrf @method('PUT')
                    @foreach ($roles as $role)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role-{{ $role->id }}" @checked($user->roles->contains('id', $role->id))>
                            <label class="form-check-label" for="role-{{ $role->id }}">{{ $role->name }}</label>
                        </div>
                    @endforeach
                    <button class="btn btn-primary btn-sm mt-2">Save Roles</button>
                </form>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-3" onsubmit="return confirm('Delete this user?');">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger btn-sm">Delete User</button>
    </form>
@endsection
