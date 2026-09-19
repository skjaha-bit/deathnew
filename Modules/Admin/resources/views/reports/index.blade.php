@extends('admin::layouts.app')

@section('title', 'Reports')

@section('content')
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Users by Status</h6>
                    <a href="{{ route('admin.reports.export-users') }}" class="btn btn-sm btn-outline-secondary">Export CSV</a>
                </div>
                <table class="table table-sm mb-0">
                    @foreach ($usersByStatus as $status => $total)
                        <tr><td class="text-capitalize">{{ $status }}</td><td class="text-end">{{ $total }}</td></tr>
                    @endforeach
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-3">
                <h6 class="mb-3">Subscriptions by Plan</h6>
                <table class="table table-sm mb-0">
                    @foreach ($subscriptionsByPlan as $plan)
                        <tr><td>{{ $plan->name }}</td><td class="text-end">{{ $plan->subscriptions_count }}</td></tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection
