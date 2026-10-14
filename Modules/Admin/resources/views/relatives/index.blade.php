@extends('admin::layouts.app')

@section('title', 'Relatives')

@section('content')
    @include('admin::components.page-header', ['title' => 'Relatives', 'subtitle' => 'Relatives declared by customers across all accounts.'])

    <div class="card p-3 mb-3">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search name or owner email" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    @foreach (['pending', 'invited', 'accepted'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" type="submit">Filter</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Relative</th>
                        <th>Relation</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Invited</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($relatives as $relative)
                        <tr>
                            <td>{{ $relative->name }} <div class="text-muted small">{{ $relative->email }}</div></td>
                            <td>{{ $relative->relation ?? '—' }}</td>
                            <td>
                                @if ($relative->user)
                                    <a href="{{ route('admin.users.show', $relative->user) }}">{{ $relative->user->name }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $relative->invite_status === 'accepted' ? 'success' : ($relative->invite_status === 'invited' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($relative->invite_status) }}
                                </span>
                            </td>
                            <td>{{ $relative->invited_at?->format('Y-m-d') ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.relatives.show', $relative) }}" class="btn btn-sm btn-outline-primary">View</a>
                                <form method="POST" action="{{ route('admin.relatives.destroy', $relative) }}" class="d-inline" onsubmit="return confirm('Remove this relative entry?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No relatives found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $relatives->links() }}</div>
@endsection
