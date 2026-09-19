@extends('admin::layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card p-3">
                <div class="text-muted small">Total Users</div>
                <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card p-3">
                <div class="text-muted small">Active Users</div>
                <div class="stat-value">{{ number_format($stats['active_users']) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card p-3">
                <div class="text-muted small">Pending Relative Invites</div>
                <div class="stat-value">{{ number_format($stats['pending_relative_invites']) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card p-3">
                <div class="text-muted small">Active Subscriptions</div>
                <div class="stat-value">{{ number_format($stats['active_subscriptions']) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-7">
            <div class="card p-3">
                <h6 class="mb-3">Sign-ups (last 14 days)</h6>
                <canvas id="signupsChart" height="110"></canvas>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card p-3">
                <h6 class="mb-3">Subscription Plan Breakdown</h6>
                <canvas id="planChart" height="110"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const signupsData = @json($signupsByDay);
    new Chart(document.getElementById('signupsChart'), {
        type: 'line',
        data: {
            labels: Object.keys(signupsData),
            datasets: [{ label: 'Sign-ups', data: Object.values(signupsData), borderColor: '#1f5c4f', backgroundColor: 'rgba(31,92,79,.15)', tension: .35, fill: true }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });

    const planData = @json($planBreakdown);
    new Chart(document.getElementById('planChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(planData),
            datasets: [{ data: Object.values(planData), backgroundColor: ['#1f5c4f', '#e0a458', '#7fb3a3', '#c9c9c9'] }]
        }
    });
</script>
@endpush
