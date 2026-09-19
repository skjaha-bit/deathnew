@extends('admin::layouts.app')

@section('title', 'Emergency Access')

@section('content')
    @include('admin::components.page-header', ['title' => 'Emergency Access', 'subtitle' => 'Two-admin approval required before a deceased customer\'s vault can be viewed.'])

    @if ($requestUser)
        <div class="card p-3 mb-3 border-warning">
            @if ($myActiveGrant)
                <h6 class="mb-2">Vault unlocked for {{ $requestUser->name }}</h6>
                <p class="text-muted small mb-2">Access expires {{ $myActiveGrant->access_expires_at->format('Y-m-d H:i') }}.</p>
                <a href="{{ route('admin.emergency-access.vault', $requestUser) }}" class="btn btn-sm btn-warning">Open Vault</a>
            @else
                <h6 class="mb-2">Request access to {{ $requestUser->name }}'s vault</h6>
                <form method="POST" action="{{ route('admin.emergency-access.request', $requestUser) }}">
                    @csrf
                    <label class="form-label small text-muted">Reason for this request</label>
                    <textarea name="reason" class="form-control mb-2" rows="2" required></textarea>
                    <button class="btn btn-sm btn-warning">Request Access</button>
                </form>
            @endif
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Target</th>
                        <th>Requested by</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Approver</th>
                        <th>Expires</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $req)
                        <tr>
                            <td><a href="{{ route('admin.users.show', $req->targetUser) }}">{{ $req->targetUser->name }}</a></td>
                            <td>{{ $req->requester->name }}</td>
                            <td class="small">{{ \Illuminate\Support\Str::limit($req->reason, 60) }}</td>
                            <td>
                                <span class="badge bg-{{ match($req->status) {
                                    'approved' => 'success',
                                    'pending' => 'warning',
                                    'denied', 'revoked' => 'danger',
                                    default => 'secondary',
                                } }}">{{ ucfirst($req->status) }}</span>
                            </td>
                            <td>{{ $req->approver?->name ?? '—' }}</td>
                            <td>{{ $req->access_expires_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td class="text-end">
                                @if ($req->status === 'pending' && $req->requested_by !== auth()->id())
                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $req->id }}">Approve</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#denyModal{{ $req->id }}">Deny</button>
                                @elseif ($req->status === 'pending')
                                    <span class="text-muted small">Awaiting another admin</span>
                                @elseif ($req->status === 'approved')
                                    <form method="POST" action="{{ route('admin.emergency-access.revoke', $req) }}" onsubmit="return confirm('Revoke this access now?');">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-secondary">Revoke</button>
                                    </form>
                                @endif
                            </td>
                        </tr>

                        @if ($req->status === 'pending' && $req->requested_by !== auth()->id())
                            <div class="modal fade" id="approveModal{{ $req->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.emergency-access.approve', $req) }}">
                                            @csrf
                                            <div class="modal-header"><h5 class="modal-title">Approve access to {{ $req->targetUser->name }}'s vault</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                            <div class="modal-body">
                                                <p class="small text-muted">Requested by {{ $req->requester->name }}: "{{ $req->reason }}"</p>
                                                <label class="form-label small text-muted">Your approval note (optional)</label>
                                                <textarea name="decision_reason" class="form-control" rows="2"></textarea>
                                            </div>
                                            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-success">Approve</button></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="denyModal{{ $req->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.emergency-access.deny', $req) }}">
                                            @csrf
                                            <div class="modal-header"><h5 class="modal-title">Deny access request</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                            <div class="modal-body">
                                                <label class="form-label small text-muted">Reason for denial (required)</label>
                                                <textarea name="decision_reason" class="form-control" rows="2" required></textarea>
                                            </div>
                                            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-danger">Deny</button></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No emergency access requests yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $requests->links() }}</div>
@endsection
