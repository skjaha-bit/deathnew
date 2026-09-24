@extends('admin::layouts.app')

@section('title', 'Customers')

@section('content')
    <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h4 class="mb-1">Customers</h4>
            <p class="text-muted mb-0 small">{{ number_format($users->total()) }} registered {{ \Illuminate\Support\Str::plural('customer', $users->total()) }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body border-bottom py-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="fy-search">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All statuses</option>
                        @foreach (['active', 'suspended', 'deceased'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-auto d-flex gap-2">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-funnel"></i>Filter</button>
                    @if (request()->filled('search') || request()->filled('status'))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Plan</th>
                        <th>Relatives</th>
                        <th>Registered</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        @php
                            $initials = collect(preg_split('/\s+/', trim($user->name ?? '')))->filter()->take(2)
                                ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fy-avatar sm">{{ $initials ?: '?' }}</span>
                                    <div class="min-w-0">
                                        <div class="cell-primary">{{ $user->name }}</div>
                                        <div class="cell-sub">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'suspended' ? 'warning' : 'secondary') }}">{{ ucfirst($user->status) }}</span></td>
                            <td>{{ $user->subscription?->plan?->name ?? '—' }}</td>
                            <td><span class="text-muted"><i class="bi bi-people me-1"></i>{{ $user->relatives_count }}</span></td>
                            <td class="text-muted">{{ $user->created_at->format('M j, Y') }}</td>
                            <td class="text-end"><a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary">View<i class="bi bi-arrow-right"></i></a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-people d-block mb-2" style="font-size:1.6rem;opacity:.5"></i>
                                No customers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>
@endsection
