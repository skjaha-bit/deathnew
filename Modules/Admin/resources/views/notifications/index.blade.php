@extends('admin::layouts.app')

@section('title', 'Notifications')

@section('content')
    <div class="card p-3 mb-3">
        <h6 class="mb-3">Broadcast a Notification</h6>
        <form method="POST" action="{{ route('admin.notifications.store') }}">
            @csrf
            <div class="mb-2">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Message</label>
                <textarea name="message" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Audience</label>
                <select name="audience" class="form-select">
                    <option value="all">All users</option>
                    <option value="active">Active users only</option>
                </select>
            </div>
            <button class="btn btn-primary">Send</button>
        </form>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>Sent</th><th>Title</th><th>Message</th></tr></thead>
                <tbody>
                    @forelse ($recent as $n)
                        <tr>
                            <td class="text-nowrap">{{ $n->created_at }}</td>
                            <td>{{ $n->data['title'] ?? '—' }}</td>
                            <td>{{ $n->data['message'] ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">No notifications sent yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
