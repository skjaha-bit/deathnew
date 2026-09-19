@extends('admin::layouts.app')

@section('title', 'Admin Users')

@section('content')
    @include('admin::components.page-header', [
        'title' => 'Admin Users',
        'subtitle' => 'Accounts with access to this admin panel — separate from your customers.',
        'actionsView' => 'admin::admin-users._create-button',
    ])

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Added</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @foreach ($user->roles as $role)
                                    <span class="badge bg-{{ $role->name === 'super-admin' ? 'warning' : 'secondary' }}">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                            <td class="text-end">
                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.admin-users.destroy', $user) }}" onsubmit="return confirm('Revoke admin access for {{ $user->name }}?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Revoke access</button>
                                    </form>
                                @else
                                    <span class="text-muted small">This is you</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No admin users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>
@endsection
