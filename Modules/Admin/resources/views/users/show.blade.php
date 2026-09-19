@extends('admin::layouts.app')

@section('title', 'User: '.$user->name)

@section('content')
    @include('admin::components.page-header', ['title' => $user->name, 'subtitle' => $user->email])

    <div class="row g-3 mb-3">
        <div class="col-md-4">@include('admin::components.stat-card', ['label' => 'Profile completion', 'value' => $overview['completion_percent'].'%', 'icon' => 'bi-clipboard-check'])</div>
        <div class="col-md-4">@include('admin::components.stat-card', ['label' => 'Relatives declared', 'value' => $user->relatives->count(), 'icon' => 'bi-person-hearts'])</div>
        <div class="col-md-4">@include('admin::components.stat-card', ['label' => 'Account status', 'value' => ucfirst($user->status), 'icon' => 'bi-shield-check'])</div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card p-3 mb-3">
                <h6 class="mb-3">Profile</h6>
                <dl class="row mb-0">
                    <dt class="col-4">Name</dt><dd class="col-8">{{ $user->name }}</dd>
                    <dt class="col-4">Email</dt><dd class="col-8">{{ $user->email }}</dd>
                    <dt class="col-4">Phone</dt><dd class="col-8">{{ $user->phone_code }} {{ $user->phone_number }}</dd>
                    <dt class="col-4">Kennitala</dt><dd class="col-8">{{ $user->kennitala ?? '—' }}</dd>
                    <dt class="col-4">Registered</dt><dd class="col-8">{{ $user->created_at }}</dd>
                </dl>
            </div>

            <div class="card p-3">
                <h6 class="mb-3">Profile completion</h6>
                <div class="progress mb-3" style="height: 8px;">
                    <div class="progress-bar" style="width: {{ $overview['completion_percent'] }}%"></div>
                </div>
                <div class="row row-cols-2 g-2 small">
                    @foreach ($overview['modules'] as $module => $done)
                        <div class="col d-flex align-items-center gap-2">
                            <i class="bi {{ $done ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted' }}"></i>
                            {{ ucwords(str_replace('_', ' ', $module)) }}
                        </div>
                    @endforeach
                </div>
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

            <div class="card p-3 mb-3">
                <h6 class="mb-3">Subscription &amp; billing</h6>
                @if ($user->subscription)
                    <dl class="row mb-0">
                        <dt class="col-5">Plan</dt><dd class="col-7">{{ $user->subscription->plan?->name ?? '—' }}</dd>
                        <dt class="col-5">Status</dt><dd class="col-7">{{ ucfirst($user->subscription->status) }}</dd>
                    </dl>
                @else
                    <p class="text-muted mb-0 small">No subscription plan selected.</p>
                @endif

                @if ($user->userSubscriptions->isNotEmpty())
                    <hr>
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Type</th><th>Amount</th><th>Cycle</th></tr></thead>
                        <tbody>
                            @foreach ($user->userSubscriptions as $sub)
                                <tr>
                                    <td>{{ $sub->subscriptionType?->name ?? $sub->name }}</td>
                                    <td>{{ $sub->amount }}</td>
                                    <td>{{ $sub->billing_cycle ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="card p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Relatives</h6>
                    <a href="{{ route('admin.relatives.index') }}?search={{ urlencode($user->email) }}" class="small">View in Relatives &rarr;</a>
                </div>
                @forelse ($user->relatives as $relative)
                    <div class="d-flex justify-content-between align-items-center small mb-2">
                        <span>{{ $relative->name }} <span class="text-muted">({{ $relative->relation ?? 'relative' }})</span></span>
                        <span class="badge bg-{{ $relative->invite_status === 'accepted' ? 'success' : 'secondary' }}">{{ ucfirst($relative->invite_status) }}</span>
                    </div>
                @empty
                    <p class="text-muted mb-0 small">No relatives declared yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-3" onsubmit="return confirm('Delete this user?');">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger btn-sm">Delete User</button>
    </form>
@endsection
