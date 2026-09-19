<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · FYRIRHYGGJA Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f5f6f8; }
        .sidebar { min-height: 100vh; background: #14342e; }
        .sidebar .nav-link { color: rgba(255,255,255,.75); }
        .sidebar .nav-link.active, .sidebar .nav-link:hover { color: #fff; background: rgba(255,255,255,.08); border-radius: .375rem; }
        .sidebar .nav-header { color: rgba(255,255,255,.4); font-size: .72rem; text-transform: uppercase; letter-spacing: .05em; }
        .brand { color: #fff; font-weight: 600; }
        .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        .stat-card .stat-value { font-size: 1.75rem; font-weight: 700; }
    </style>
    @stack('styles')
</head>
<body>
<div class="d-flex">
    <nav class="sidebar p-3" style="width: 260px; position: sticky; top: 0; height: 100vh; overflow-y: auto;">
        <a href="{{ route('admin.dashboard') }}" class="brand d-block mb-4 text-decoration-none fs-5">🌿 FYRIRHYGGJA</a>
        <ul class="nav nav-pills flex-column gap-1">
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>

            <li class="nav-header mt-3 mb-1 px-2">Users &amp; Access</li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people me-2"></i>Users</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}"><i class="bi bi-shield-lock me-2"></i>Roles &amp; Permissions</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.relatives.*') ? 'active' : '' }}" href="{{ route('admin.relatives.index') }}"><i class="bi bi-person-hearts me-2"></i>Relatives</a></li>

            <li class="nav-header mt-3 mb-1 px-2">Reference Data</li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.pension-funds.*') ? 'active' : '' }}" href="{{ route('admin.pension-funds.index') }}"><i class="bi bi-piggy-bank me-2"></i>Pension Funds</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.insurance-companies.*') ? 'active' : '' }}" href="{{ route('admin.insurance-companies.index') }}"><i class="bi bi-shield-check me-2"></i>Insurance Companies</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.banks.*') ? 'active' : '' }}" href="{{ route('admin.banks.index') }}"><i class="bi bi-bank me-2"></i>Banks</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.trade-unions.*') ? 'active' : '' }}" href="{{ route('admin.trade-unions.index') }}"><i class="bi bi-briefcase me-2"></i>Trade Unions</a></li>

            <li class="nav-header mt-3 mb-1 px-2">Content</li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.cms.*') ? 'active' : '' }}" href="{{ route('admin.cms.index') }}"><i class="bi bi-file-earmark-text me-2"></i>CMS Content</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}" href="{{ route('admin.banners.index') }}"><i class="bi bi-image me-2"></i>Banners</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.languages.*') ? 'active' : '' }}" href="{{ route('admin.languages.index') }}"><i class="bi bi-translate me-2"></i>Languages</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.translations.*') ? 'active' : '' }}" href="{{ route('admin.translations.index') }}"><i class="bi bi-chat-square-text me-2"></i>Translations</a></li>

            <li class="nav-header mt-3 mb-1 px-2">Commerce</li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.subscription-plans.*') ? 'active' : '' }}" href="{{ route('admin.subscription-plans.index') }}"><i class="bi bi-credit-card me-2"></i>Subscription Plans</a></li>

            <li class="nav-header mt-3 mb-1 px-2">System</li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}"><i class="bi bi-bell me-2"></i>Notifications</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}"><i class="bi bi-gear me-2"></i>Settings</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}" href="{{ route('admin.activity-logs.index') }}"><i class="bi bi-clock-history me-2"></i>Activity Logs</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}"><i class="bi bi-graph-up me-2"></i>Reports</a></li>
        </ul>
    </nav>

    <main class="flex-grow-1">
        <nav class="navbar navbar-expand bg-white border-bottom px-4 py-2">
            <span class="fw-semibold">@yield('title', 'Dashboard')</span>
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="text-muted small">{{ auth()->user()?->name }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary" type="submit">Logout</button>
                </form>
            </div>
        </nav>

        <div class="p-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>
