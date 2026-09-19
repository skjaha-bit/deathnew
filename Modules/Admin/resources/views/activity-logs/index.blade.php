@extends('admin::layouts.app')

@section('title', 'Activity Logs')

@section('content')
    <div class="card p-3 mb-3">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <select name="log_name" class="form-select">
                    <option value="">All logs</option>
                    @foreach ($logNames as $name)
                        <option value="{{ $name }}" @selected(request('log_name') === $name)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100">Filter</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle">
                <thead class="table-light">
                    <tr><th>When</th><th>Log</th><th>Description</th><th>Causer</th><th>Subject</th></tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="text-nowrap">{{ $log->created_at }}</td>
                            <td>{{ $log->log_name }}</td>
                            <td>{{ $log->description }}</td>
                            <td>{{ $log->causer?->name ?? '—' }}</td>
                            <td>{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No activity recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $logs->links() }}</div>
@endsection
