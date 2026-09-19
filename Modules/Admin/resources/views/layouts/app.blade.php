<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · FYRIRHYGGJA Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --fy-teal-900: #14342e;
            --fy-teal-700: #1f5049;
            --fy-teal-500: #2f7a6c;
            --fy-gold-400: #d9ad4f;
            --fy-gold-600: #b6862c;
            --fy-cream: #f5f0e6;
            --fy-cream-50: #faf7f0;
            --fy-ink: #1c2a27;
            --fy-radius-pill: 999px;
            --fy-radius-card: 16px;
            --fy-shadow-card: 0 4px 16px rgba(20,52,46,.06);
        }

        body { background-color: var(--fy-cream); font-family: 'Plus Jakarta Sans', system-ui, sans-serif; color: var(--fy-ink); }
        h1, h2, h3, h4, h5, h6 { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; font-weight: 700; }

        .sidebar { min-height: 100vh; background: linear-gradient(180deg, var(--fy-teal-900), #0e2622); }
        .sidebar .nav-link { color: rgba(255,255,255,.75); }
        .sidebar .nav-link.active, .sidebar .nav-link:hover { color: var(--fy-gold-400); background: rgba(217,173,79,.14); border-radius: .5rem; }
        .sidebar .nav-link.disabled { color: rgba(255,255,255,.35); pointer-events: none; }
        .sidebar .nav-header { color: rgba(255,255,255,.4); font-size: .72rem; text-transform: uppercase; letter-spacing: .05em; }
        .sidebar .nav-toggle { color: rgba(255,255,255,.75); cursor: pointer; }
        .sidebar .nav-toggle:hover { color: var(--fy-gold-400); }
        .sidebar .nav-toggle .bi-chevron-down { transition: transform .15s ease; }
        .sidebar .nav-toggle[aria-expanded="true"] .bi-chevron-down { transform: rotate(180deg); }

        .brand {
            font-weight: 800;
            font-size: 1.15rem;
            text-decoration: none;
            background: linear-gradient(135deg, #fff, var(--fy-gold-400));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .card { border: none; border-radius: var(--fy-radius-card); box-shadow: var(--fy-shadow-card); }
        .card-header { background: var(--fy-cream-50); border-bottom: 1px solid rgba(0,0,0,.04); border-radius: var(--fy-radius-card) var(--fy-radius-card) 0 0 !important; }
        .stat-card .stat-value { font-size: 1.75rem; font-weight: 700; }
        .stat-card .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: rgba(47,122,108,.12); color: var(--fy-teal-500); font-size: 1.2rem; }

        .btn-primary { background: linear-gradient(135deg, var(--fy-gold-400), var(--fy-gold-600)); border: none; border-radius: var(--fy-radius-pill); font-weight: 600; }
        .btn-primary:hover, .btn-primary:focus { filter: brightness(1.06); color: #fff; }
        .btn, .form-select, .form-control { border-radius: 10px; }
        .btn-sm { border-radius: var(--fy-radius-pill); }

        .table thead th { background: var(--fy-cream-50); text-transform: uppercase; font-size: .72rem; letter-spacing: .05em; color: #6b7f7a; border-bottom: none; }

        .badge.bg-success { background-color: var(--fy-teal-500) !important; }
        .badge.bg-warning { background-color: var(--fy-gold-400) !important; color: var(--fy-teal-900) !important; }
    </style>
    @stack('styles')
</head>
<body>
@php
    $user = auth()->user();
    $can = fn (?string $permission) => $permission === null || ($user?->can($permission) ?? false);
    $routeExists = fn (string $name) => \Illuminate\Support\Facades\Route::has($name);

    $navGroups = [
        'Customer Management' => [
            ['route' => 'admin.users.index', 'match' => 'admin.users.*', 'icon' => 'bi-people', 'label' => 'Customers', 'can' => 'manage-users'],
            ['route' => 'admin.relatives.index', 'match' => 'admin.relatives.*', 'icon' => 'bi-person-hearts', 'label' => 'Relatives', 'can' => 'manage-users'],
        ],
        'Users & Access' => [
            ['route' => 'admin.admin-users.index', 'match' => 'admin.admin-users.*', 'icon' => 'bi-person-badge', 'label' => 'Admin Users', 'can' => 'manage-admins'],
            ['route' => 'admin.roles.index', 'match' => 'admin.roles.*', 'icon' => 'bi-shield-lock', 'label' => 'Roles & Permissions', 'can' => 'manage-roles'],
        ],
        'Reference Data' => [
            ['route' => 'admin.banks.index', 'match' => 'admin.banks.*', 'icon' => 'bi-bank', 'label' => 'Banks', 'can' => 'manage-reference-data'],
            ['route' => 'admin.loan-types.index', 'match' => 'admin.loan-types.*', 'icon' => 'bi-cash-coin', 'label' => 'Loan Types', 'can' => 'manage-reference-data'],
            ['route' => 'admin.account-types.index', 'match' => 'admin.account-types.*', 'icon' => 'bi-wallet2', 'label' => 'Account Types', 'can' => 'manage-reference-data'],
            ['route' => 'admin.bond-types.index', 'match' => 'admin.bond-types.*', 'icon' => 'bi-file-earmark-ruled', 'label' => 'Bond Types', 'can' => 'manage-reference-data'],
            ['route' => 'admin.fund-types.index', 'match' => 'admin.fund-types.*', 'icon' => 'bi-graph-up-arrow', 'label' => 'Fund Types', 'can' => 'manage-reference-data'],
            ['route' => 'admin.insurance-companies.index', 'match' => 'admin.insurance-companies.*', 'icon' => 'bi-shield-check', 'label' => 'Insurance Companies', 'can' => 'manage-reference-data'],
            ['route' => 'admin.insurance-types.index', 'match' => 'admin.insurance-types.*', 'icon' => 'bi-umbrella', 'label' => 'Insurance Types', 'can' => 'manage-reference-data'],
            ['route' => 'admin.pension-funds.index', 'match' => 'admin.pension-funds.*', 'icon' => 'bi-piggy-bank', 'label' => 'Pension Funds', 'can' => 'manage-reference-data'],
            ['route' => 'admin.trade-unions.index', 'match' => 'admin.trade-unions.*', 'icon' => 'bi-briefcase', 'label' => 'Trade Unions', 'can' => 'manage-reference-data'],
            ['route' => 'admin.property-types.index', 'match' => 'admin.property-types.*', 'icon' => 'bi-house', 'label' => 'Property Types', 'can' => 'manage-reference-data'],
            ['route' => 'admin.ceremony-types.index', 'match' => 'admin.ceremony-types.*', 'icon' => 'bi-flower1', 'label' => 'Ceremony Types', 'can' => 'manage-reference-data'],
            ['route' => 'admin.arrangement-types.index', 'match' => 'admin.arrangement-types.*', 'icon' => 'bi-list-check', 'label' => 'Arrangement Types', 'can' => 'manage-reference-data'],
            ['route' => 'admin.funeral-service-types.index', 'match' => 'admin.funeral-service-types.*', 'icon' => 'bi-flag', 'label' => 'Funeral Service Types', 'can' => 'manage-reference-data'],
        ],
        'CMS Management' => [
            ['route' => 'admin.banners.index', 'match' => 'admin.banners.*', 'icon' => 'bi-image', 'label' => 'Banners', 'can' => 'manage-content'],
            ['route' => 'admin.cms-contents.index', 'match' => 'admin.cms-contents.*', 'icon' => 'bi-file-earmark-text', 'label' => 'CMS Content', 'can' => 'manage-content'],
        ],
        'Language Management' => [
            ['route' => 'admin.languages.index', 'match' => 'admin.languages.*', 'icon' => 'bi-translate', 'label' => 'Languages', 'can' => 'manage-settings'],
            ['route' => 'admin.translations.index', 'match' => 'admin.translations.*', 'icon' => 'bi-chat-square-text', 'label' => 'Translations', 'can' => 'manage-settings'],
        ],
        'Commerce' => [
            ['route' => 'admin.subscription-plans.index', 'match' => 'admin.subscription-plans.*', 'icon' => 'bi-credit-card', 'label' => 'Subscription Plans', 'can' => 'manage-subscriptions'],
            ['route' => 'admin.subscription-types.index', 'match' => 'admin.subscription-types.*', 'icon' => 'bi-tags', 'label' => 'Subscription Types', 'can' => 'manage-subscriptions'],
        ],
        'Vault & Emergency Access' => [
            ['route' => 'admin.emergency-access.index', 'match' => 'admin.emergency-access.*', 'icon' => 'bi-shield-lock-fill', 'label' => 'Emergency Access', 'can' => 'request-emergency-access'],
            ['route' => 'admin.death-reports.index', 'match' => 'admin.death-reports.*', 'icon' => 'bi-file-earmark-medical', 'label' => 'Deceased Reports', 'can' => 'mark-user-deceased'],
        ],
        'System' => [
            ['route' => 'admin.notifications.index', 'match' => 'admin.notifications.*', 'icon' => 'bi-bell', 'label' => 'Notifications', 'can' => 'send-notifications'],
            ['route' => 'admin.settings.index', 'match' => 'admin.settings.*', 'icon' => 'bi-gear', 'label' => 'Settings', 'can' => 'manage-settings'],
            ['route' => 'admin.activity-logs.index', 'match' => 'admin.activity-logs.*', 'icon' => 'bi-clock-history', 'label' => 'Activity Logs', 'can' => 'view-activity-logs'],
            ['route' => 'admin.reports.index', 'match' => 'admin.reports.*', 'icon' => 'bi-graph-up', 'label' => 'Reports', 'can' => 'view-reports'],
        ],
    ];

    $visibleGroups = [];
    foreach ($navGroups as $groupLabel => $items) {
        $visibleItems = array_values(array_filter($items, fn ($item) => $routeExists($item['route']) && $can($item['can'] ?? null)));
        if (! empty($visibleItems)) {
            $visibleGroups[$groupLabel] = $visibleItems;
        }
    }
@endphp
<div class="d-flex">
    <nav class="sidebar p-3" style="width: 270px; position: sticky; top: 0; height: 100vh; overflow-y: auto;">
        <a href="{{ route('admin.dashboard') }}" class="brand d-block mb-4">🌿 FYRIRHYGGJA</a>
        <ul class="nav nav-pills flex-column gap-1">
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>

            @foreach ($visibleGroups as $groupLabel => $items)
                <li class="nav-header mt-3 mb-1 px-2">{{ $groupLabel }}</li>
                @if (count($items) > 6)
                    @php $collapseId = 'nav-collapse-' . \Illuminate\Support\Str::slug($groupLabel); @endphp
                    @php $groupActive = collect($items)->contains(fn ($item) => request()->routeIs($item['match'])); @endphp
                    <li class="nav-item">
                        <a class="nav-link nav-toggle d-flex align-items-center" data-bs-toggle="collapse" href="#{{ $collapseId }}" role="button" aria-expanded="{{ $groupActive ? 'true' : 'false' }}">
                            <i class="bi bi-collection me-2"></i>
                            <span class="flex-grow-1">{{ $groupLabel }}</span>
                            <i class="bi bi-chevron-down small"></i>
                        </a>
                        <div class="collapse {{ $groupActive ? 'show' : '' }}" id="{{ $collapseId }}">
                            <ul class="nav nav-pills flex-column gap-1 ps-3">
                                @foreach ($items as $item)
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs($item['match']) ? 'active' : '' }}" href="{{ route($item['route']) }}"><i class="bi {{ $item['icon'] }} me-2"></i>{{ $item['label'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                @else
                    @foreach ($items as $item)
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs($item['match']) ? 'active' : '' }}" href="{{ route($item['route']) }}"><i class="bi {{ $item['icon'] }} me-2"></i>{{ $item['label'] }}</a></li>
                    @endforeach
                @endif
            @endforeach
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

@include('admin::components.toast')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>
