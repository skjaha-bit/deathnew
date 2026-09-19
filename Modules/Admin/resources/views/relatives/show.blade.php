@extends('admin::layouts.app')

@section('title', 'Relative: '.$relative->name)

@section('content')
    @include('admin::components.page-header', ['title' => $relative->name, 'subtitle' => $relative->email])

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card p-3">
                <h6 class="mb-3">Details</h6>
                <dl class="row mb-0">
                    <dt class="col-4">Name</dt><dd class="col-8">{{ $relative->name }}</dd>
                    <dt class="col-4">Email</dt><dd class="col-8">{{ $relative->email ?? '—' }}</dd>
                    <dt class="col-4">Phone</dt><dd class="col-8">{{ $relative->phone ?? '—' }}</dd>
                    <dt class="col-4">Relation</dt><dd class="col-8">{{ $relative->relation ?? '—' }}</dd>
                    <dt class="col-4">Status</dt><dd class="col-8">{{ ucfirst($relative->invite_status) }}</dd>
                    <dt class="col-4">Invited</dt><dd class="col-8">{{ $relative->invited_at?->format('Y-m-d H:i') ?? '—' }}</dd>
                    <dt class="col-4">Accepted</dt><dd class="col-8">{{ $relative->accepted_at?->format('Y-m-d H:i') ?? '—' }}</dd>
                </dl>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-3 mb-3">
                <h6 class="mb-3">Declared by</h6>
                @if ($relative->user)
                    <a href="{{ route('admin.users.show', $relative->user) }}">{{ $relative->user->name }} ({{ $relative->user->email }})</a>
                @else
                    <span class="text-muted">Unknown customer</span>
                @endif
            </div>

            <div class="card p-3">
                <h6 class="mb-3">Granted permissions</h6>
                @forelse ($relative->permissions ?? [] as $permission)
                    <span class="badge bg-secondary me-1 mb-1">{{ $permission }}</span>
                @empty
                    <span class="text-muted">None granted yet.</span>
                @endforelse
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.relatives.destroy', $relative) }}" class="mt-3" onsubmit="return confirm('Remove this relative entry?');">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger btn-sm">Remove Relative</button>
    </form>
@endsection
