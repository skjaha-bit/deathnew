@extends('admin::layouts.app')

@section('title', 'Deceased Reports')

@section('content')
    @include('admin::components.page-header', ['title' => 'Deceased Reports', 'subtitle' => 'Death reports submitted by relatives, pending confirmation.'])

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Customer</th>
                        <th>Reported by</th>
                        <th>Relationship</th>
                        <th>Notes</th>
                        <th>Reported</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        <tr>
                            <td><a href="{{ route('admin.users.show', $report->user) }}">{{ $report->user->name }}</a></td>
                            <td>{{ $report->reporter_name }}<div class="text-muted small">{{ $report->reporter_email }} {{ $report->reporter_phone }}</div></td>
                            <td>{{ $report->relationship ?? '—' }}</td>
                            <td class="small">{{ \Illuminate\Support\Str::limit($report->notes, 80) ?: '—' }}</td>
                            <td>{{ $report->reported_at->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                @if ($report->status === 'pending')
                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#confirmModal{{ $report->id }}">Confirm</button>
                                    <form method="POST" action="{{ route('admin.death-reports.reject', $report) }}" class="d-inline" onsubmit="return confirm('Reject this report?');">
                                        @csrf @method('POST')
                                        <button class="btn btn-sm btn-outline-secondary">Reject</button>
                                    </form>
                                @else
                                    <span class="badge bg-{{ $report->status === 'confirmed' ? 'success' : 'secondary' }}">{{ ucfirst($report->status) }}</span>
                                @endif
                            </td>
                        </tr>

                        <div class="modal fade" id="confirmModal{{ $report->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.death-reports.confirm', $report) }}">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirm death for {{ $report->user->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label class="form-label small text-muted">Reason / basis for confirmation</label>
                                            <textarea name="reason" class="form-control" rows="3" required></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">Confirm death</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No pending reports.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $reports->links() }}</div>
@endsection
