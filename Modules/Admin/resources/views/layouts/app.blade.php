<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · FYRIRHYGGJA Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --fy-teal-900: #12302a;
            --fy-teal-800: #173c35;
            --fy-teal-700: #1f5049;
            --fy-teal-600: #266358;
            --fy-teal-500: #2f7a6c;
            --fy-teal-50: #e8f3f0;
            --fy-gold-400: #d9ad4f;
            --fy-gold-600: #b6862c;
            --fy-gold-50: #fbf3e1;
            --fy-cream: #f5f0e6;
            --fy-cream-50: #faf7f0;
            --fy-bg: #f4f5f3;
            --fy-surface: #ffffff;
            --fy-border: #e6e9e7;
            --fy-border-strong: #d6dbd8;
            --fy-ink: #1a2724;
            --fy-muted: #6b7a76;
            --fy-radius-pill: 999px;
            --fy-radius-card: 14px;
            --fy-radius-control: 9px;
            --fy-shadow-card: 0 1px 2px rgba(18,48,42,.04), 0 4px 14px rgba(18,48,42,.04);
            --fy-sidebar-w: 256px;

            --bs-body-font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
            --bs-body-font-size: .875rem;
            --bs-body-color: var(--fy-ink);
            --bs-body-bg: var(--fy-bg);
            --bs-border-color: var(--fy-border);
            --bs-secondary-color: var(--fy-muted);
            --bs-link-color: var(--fy-teal-500);
            --bs-link-color-rgb: 47,122,108;
            --bs-link-hover-color: var(--fy-teal-700);
            --bs-link-hover-color-rgb: 31,80,73;
            --bs-primary: var(--fy-teal-500);
            --bs-primary-rgb: 47,122,108;
            --bs-focus-ring-color: rgba(47,122,108,.2);
        }

        html { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        body { background-color: var(--fy-bg); font-family: var(--bs-body-font-family); font-size: .875rem; color: var(--fy-ink); line-height: 1.55; letter-spacing: -.003em; }
        h1, h2, h3, h4, h5, h6 { font-family: var(--bs-body-font-family); font-weight: 600; color: var(--fy-ink); letter-spacing: -.015em; }
        h4, .h4 { font-size: 1.3rem; }
        h5, .h5 { font-size: 1.05rem; }
        h6, .h6 { font-size: .9rem; }
        .text-muted { color: var(--fy-muted) !important; }
        .small, small { font-size: .8rem; }
        a { text-decoration: none; }
        ::selection { background: rgba(217,173,79,.3); }

        /* ---------- Shell ---------- */
        .fy-shell { display: flex; min-height: 100vh; }
        .fy-main { flex: 1 1 auto; min-width: 0; display: flex; flex-direction: column; }
        .fy-content { padding: 1.75rem 2rem 2.5rem; flex: 1; }

        /* ---------- Sidebar ---------- */
        .sidebar {
            width: var(--fy-sidebar-w);
            flex: 0 0 var(--fy-sidebar-w);
            position: sticky; top: 0;
            height: 100vh;
            display: flex; flex-direction: column;
            background: linear-gradient(180deg, var(--fy-teal-800) 0%, var(--fy-teal-900) 100%);
            color: rgba(255,255,255,.72);
            z-index: 1040;
        }
        .sidebar-brand { display: flex; align-items: center; gap: .7rem; padding: 1.25rem 1.25rem 1.1rem; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,.06); }
        .sidebar-brand .brand-mark {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, var(--fy-gold-400), var(--fy-gold-600));
            color: var(--fy-teal-900); font-size: 1.1rem;
            box-shadow: 0 4px 12px rgba(217,173,79,.25);
        }
        .sidebar-brand .brand-name { color: #fff; font-weight: 700; font-size: .95rem; letter-spacing: .04em; line-height: 1.1; }
        .sidebar-brand .brand-sub { color: rgba(255,255,255,.45); font-size: .68rem; font-weight: 500; letter-spacing: .08em; text-transform: uppercase; }

        .sidebar-scroll { flex: 1; overflow-y: auto; padding: .75rem .75rem 1.25rem; scrollbar-width: thin; scrollbar-color: rgba(255,255,255,.15) transparent; }
        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 6px; }

        .sidebar .nav { gap: 2px !important; }
        .sidebar .nav-header { color: rgba(255,255,255,.38); font-size: .66rem; font-weight: 600; text-transform: uppercase; letter-spacing: .09em; padding: 1.1rem .75rem .4rem; list-style: none; }
        .sidebar .nav-link {
            position: relative;
            display: flex; align-items: center;
            padding: .5rem .75rem;
            color: rgba(255,255,255,.72);
            font-size: .835rem; font-weight: 500;
            border-radius: 8px;
            transition: background-color .15s ease, color .15s ease;
        }
        .sidebar .nav-link > .bi:first-child { font-size: 1rem; width: 1.25rem; margin-right: .65rem !important; opacity: .85; }
        .sidebar .nav-link:hover { color: #fff; background: rgba(255,255,255,.06); }
        .sidebar .nav-link.active { color: #fff; background: rgba(217,173,79,.16); }
        .sidebar .nav-link.active > .bi:first-child { color: var(--fy-gold-400); opacity: 1; }
        .sidebar .nav-link.active::before { content: ''; position: absolute; left: -.75rem; top: 8px; bottom: 8px; width: 3px; border-radius: 0 3px 3px 0; background: var(--fy-gold-400); }
        .sidebar .nav-link.disabled { color: rgba(255,255,255,.3); pointer-events: none; }
        .sidebar .nav-toggle { cursor: pointer; }
        .sidebar .nav-toggle .bi-chevron-down { font-size: .7rem; opacity: .6; transition: transform .2s ease; }
        .sidebar .nav-toggle[aria-expanded="true"] { color: #fff; }
        .sidebar .nav-toggle[aria-expanded="true"] .bi-chevron-down { transform: rotate(180deg); }
        .sidebar .collapse .nav, .sidebar .collapsing .nav { margin: 2px 0 4px .9rem; padding-left: .55rem !important; border-left: 1px solid rgba(255,255,255,.08); }
        .sidebar .collapse .nav-link, .sidebar .collapsing .nav-link { font-size: .8rem; padding: .42rem .65rem; }
        .sidebar .collapse .nav-link.active::before, .sidebar .collapsing .nav-link.active::before { display: none; }

        .sidebar-backdrop { display: none; }

        /* ---------- Topbar ---------- */
        .fy-topbar {
            position: sticky; top: 0; z-index: 1020;
            height: 64px;
            display: flex; align-items: center; gap: 1rem;
            padding: 0 2rem;
            background: rgba(255,255,255,.88);
            backdrop-filter: saturate(180%) blur(8px);
            border-bottom: 1px solid var(--fy-border);
        }
        .fy-topbar .page-title { font-size: 1rem; font-weight: 600; margin: 0; color: var(--fy-ink); }
        .fy-topbar .crumbs { font-size: .75rem; color: var(--fy-muted); }
        .fy-topbar .crumbs a { color: var(--fy-muted); }
        .fy-topbar .crumbs a:hover { color: var(--fy-teal-500); }
        .fy-menu-btn { display: none; border: 1px solid var(--fy-border); background: #fff; width: 38px; height: 38px; border-radius: 9px; color: var(--fy-ink); font-size: 1.2rem; }
        .fy-user-btn { display: flex; align-items: center; gap: .6rem; padding: .3rem .55rem .3rem .3rem; border: 1px solid transparent; border-radius: var(--fy-radius-pill); background: transparent; color: var(--fy-ink); transition: background .15s, border-color .15s; }
        .fy-user-btn:hover, .fy-user-btn[aria-expanded="true"] { background: #f6f8f7; border-color: var(--fy-border); }
        .fy-user-btn .name { font-weight: 600; font-size: .82rem; line-height: 1.15; text-align: left; }
        .fy-user-btn .role { font-size: .7rem; color: var(--fy-muted); line-height: 1.15; text-align: left; }
        .fy-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;
            background: linear-gradient(135deg, var(--fy-teal-500), var(--fy-teal-700));
            color: #fff; font-weight: 600; font-size: .75rem; letter-spacing: .02em;
        }
        .fy-avatar.sm { width: 30px; height: 30px; font-size: .7rem; }

        /* ---------- Cards ---------- */
        .card { border: 1px solid var(--fy-border); border-radius: var(--fy-radius-card); box-shadow: var(--fy-shadow-card); background: var(--fy-surface); }
        .card > .table-responsive:first-child, .card > .table-responsive:last-child { border-radius: var(--fy-radius-card); }
        .card-header { background: var(--fy-surface); border-bottom: 1px solid var(--fy-border); padding: .95rem 1.25rem; font-weight: 600; font-size: .9rem; border-radius: var(--fy-radius-card) var(--fy-radius-card) 0 0 !important; }
        .card-body { padding: 1.25rem; }
        .card-footer { background: #fafbfa; border-top: 1px solid var(--fy-border); border-radius: 0 0 var(--fy-radius-card) var(--fy-radius-card) !important; }
        .card-title { font-weight: 600; font-size: .95rem; }

        .stat-card { transition: transform .15s ease, box-shadow .15s ease; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(18,48,42,.08); }
        .stat-card .stat-value { font-size: 1.6rem; font-weight: 700; letter-spacing: -.02em; line-height: 1.15; }
        .stat-card .stat-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: var(--fy-teal-50); color: var(--fy-teal-500); font-size: 1.25rem; flex-shrink: 0; }

        /* ---------- Buttons ---------- */
        .btn { --bs-btn-font-size: .835rem; --bs-btn-font-weight: 500; --bs-btn-padding-y: .5rem; --bs-btn-padding-x: 1rem; border-radius: var(--fy-radius-control); transition: all .15s ease; display: inline-flex; align-items: center; justify-content: center; gap: .35rem; }
        .btn-sm { --bs-btn-font-size: .775rem; --bs-btn-padding-y: .3rem; --bs-btn-padding-x: .75rem; border-radius: 7px; }
        .btn-lg { --bs-btn-font-size: .95rem; }
        .btn:focus-visible { box-shadow: 0 0 0 .2rem rgba(47,122,108,.2); }

        .btn-primary { --bs-btn-bg: var(--fy-teal-500); --bs-btn-border-color: var(--fy-teal-500); --bs-btn-hover-bg: var(--fy-teal-600); --bs-btn-hover-border-color: var(--fy-teal-600); --bs-btn-active-bg: var(--fy-teal-700); --bs-btn-active-border-color: var(--fy-teal-700); --bs-btn-disabled-bg: var(--fy-teal-500); --bs-btn-disabled-border-color: var(--fy-teal-500); box-shadow: 0 1px 2px rgba(18,48,42,.12); }
        .btn-brand, .btn-save { background: linear-gradient(135deg, var(--fy-gold-400), var(--fy-gold-600)); border: none; color: var(--fy-teal-900); font-weight: 600; }
        .btn-brand:hover, .btn-save:hover { filter: brightness(1.05); color: var(--fy-teal-900); }
        .btn-success { --bs-btn-bg: #2f8a5f; --bs-btn-border-color: #2f8a5f; --bs-btn-hover-bg: #277650; --bs-btn-hover-border-color: #277650; }
        .btn-warning { --bs-btn-bg: var(--fy-gold-400); --bs-btn-border-color: var(--fy-gold-400); --bs-btn-color: var(--fy-teal-900); --bs-btn-hover-bg: var(--fy-gold-600); --bs-btn-hover-border-color: var(--fy-gold-600); --bs-btn-hover-color: #fff; }
        .btn-danger { --bs-btn-bg: #c2413a; --bs-btn-border-color: #c2413a; --bs-btn-hover-bg: #a8352f; --bs-btn-hover-border-color: #a8352f; }

        .btn-outline-primary { --bs-btn-color: var(--fy-teal-500); --bs-btn-border-color: #cfe3dd; --bs-btn-bg: #fff; --bs-btn-hover-bg: var(--fy-teal-50); --bs-btn-hover-color: var(--fy-teal-700); --bs-btn-hover-border-color: #b5d6cd; --bs-btn-active-bg: var(--fy-teal-50); --bs-btn-active-color: var(--fy-teal-700); --bs-btn-active-border-color: #b5d6cd; }
        .btn-outline-secondary { --bs-btn-color: #3d4b47; --bs-btn-border-color: var(--fy-border-strong); --bs-btn-bg: #fff; --bs-btn-hover-bg: #f3f5f4; --bs-btn-hover-color: var(--fy-ink); --bs-btn-hover-border-color: #c5ccc9; --bs-btn-active-bg: #eceff0; --bs-btn-active-color: var(--fy-ink); --bs-btn-active-border-color: #c5ccc9; }
        .btn-outline-danger { --bs-btn-color: #c2413a; --bs-btn-border-color: #f0cfcc; --bs-btn-bg: #fff; --bs-btn-hover-bg: #fdf1f0; --bs-btn-hover-color: #a8352f; --bs-btn-hover-border-color: #e7b7b3; --bs-btn-active-bg: #fbe6e4; --bs-btn-active-color: #a8352f; --bs-btn-active-border-color: #e7b7b3; }
        .btn-outline-warning { --bs-btn-color: #9a6f1c; --bs-btn-border-color: #ecd9ad; --bs-btn-bg: #fff; --bs-btn-hover-bg: var(--fy-gold-50); --bs-btn-hover-color: #85600f; --bs-btn-hover-border-color: #e2c888; }
        .btn-close:focus { box-shadow: 0 0 0 .2rem rgba(47,122,108,.2); }

        /* ---------- Forms ---------- */
        .form-control, .form-select { font-size: .85rem; padding: .55rem .85rem; border: 1px solid var(--fy-border-strong); border-radius: var(--fy-radius-control); color: var(--fy-ink); background-color: #fff; transition: border-color .15s ease, box-shadow .15s ease; }
        .form-select { padding-right: 2.25rem; }
        .form-control-sm, .form-select-sm { font-size: .8rem; padding: .38rem .7rem; border-radius: 8px; }
        .form-select-sm { padding-right: 2rem; }
        .form-control::placeholder { color: #9aa6a3; }
        .form-control:hover, .form-select:hover { border-color: #c2cac7; }
        .form-control:focus, .form-select:focus { border-color: var(--fy-teal-500); box-shadow: 0 0 0 3px rgba(47,122,108,.14); }
        .form-label { font-size: .8rem; font-weight: 500; color: #3d4b47; margin-bottom: .35rem; }
        .form-text { font-size: .75rem; color: var(--fy-muted); }
        .form-check-input { border-color: #c2cac7; }
        .form-check-input:checked { background-color: var(--fy-teal-500); border-color: var(--fy-teal-500); }
        .form-check-input:focus { border-color: var(--fy-teal-500); box-shadow: 0 0 0 3px rgba(47,122,108,.14); }
        .form-switch .form-check-input { width: 2.3em; height: 1.25em; cursor: pointer; }
        .form-switch .form-check-input:focus { --bs-form-switch-bg: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%232f7a6c'/%3e%3c/svg%3e"); }
        .form-switch .form-check-input:checked { --bs-form-switch-bg: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e"); }
        .input-group-text { background: #f6f8f7; border-color: var(--fy-border-strong); color: var(--fy-muted); font-size: .85rem; }

        .fy-search { position: relative; }
        .fy-search > .bi { position: absolute; left: .85rem; top: 50%; transform: translateY(-50%); color: #9aa6a3; pointer-events: none; }
        .fy-search > .form-control { padding-left: 2.3rem; }

        /* ---------- Tables ---------- */
        .table { --bs-table-bg: transparent; --bs-table-hover-bg: #f8faf9; --bs-table-striped-bg: #fafbfa; --bs-table-border-color: #eef1ef; margin-bottom: 0; color: var(--fy-ink); font-size: .845rem; }
        .table > :not(caption) > * > * { padding: .85rem 1rem; border-bottom-color: #eef1ef; }
        .table > thead > tr > th, .table thead.table-light th, .table-light > th {
            background: #f8faf9 !important;
            color: var(--fy-muted);
            font-size: .7rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .06em;
            padding-top: .75rem; padding-bottom: .75rem;
            border-bottom: 1px solid var(--fy-border);
            white-space: nowrap;
        }
        .table > tbody > tr:last-child > * { border-bottom: 0; }
        .table > tbody > tr > td:first-child, .table > thead > tr > th:first-child { padding-left: 1.25rem; }
        .table > tbody > tr > td:last-child, .table > thead > tr > th:last-child { padding-right: 1.25rem; }
        .table-hover > tbody > tr { transition: background-color .12s ease; }
        .table tr.table-light > td { --bs-table-bg: #fbfcfb; background: #fbfcfb; }
        .card-body > .table-responsive { margin: -1.25rem; border-radius: var(--fy-radius-card); }
        .card-body > .table-responsive + * { margin-top: 2.25rem; }
        .table .cell-primary { font-weight: 600; color: var(--fy-ink); }
        .table .cell-sub { font-size: .76rem; color: var(--fy-muted); }

        /* ---------- Badges (soft) ---------- */
        .badge { font-weight: 600; font-size: .7rem; padding: .35em .7em; border-radius: var(--fy-radius-pill); letter-spacing: .01em; display: inline-flex; align-items: center; gap: .35em; }
        .badge.bg-success, .badge.text-bg-success { background-color: #e3f4ec !important; color: #1f7a4f !important; }
        .badge.bg-warning, .badge.text-bg-warning { background-color: #fcf1d9 !important; color: #946513 !important; }
        .badge.bg-danger, .badge.text-bg-danger { background-color: #fbe7e5 !important; color: #b23a33 !important; }
        .badge.bg-secondary, .badge.text-bg-secondary { background-color: #eef0ef !important; color: #56635f !important; }
        .badge.bg-primary, .badge.text-bg-primary { background-color: var(--fy-teal-50) !important; color: var(--fy-teal-600) !important; }
        .badge.bg-info, .badge.text-bg-info { background-color: #e5f1f8 !important; color: #276a8f !important; }
        .badge.bg-dark, .badge.text-bg-dark { background-color: #e7eae9 !important; color: #26312e !important; }
        .badge.bg-light, .badge.text-bg-light { background-color: #f4f6f5 !important; color: #56635f !important; border: 1px solid var(--fy-border); }
        .badge.bg-success::before, .badge.bg-warning::before, .badge.bg-danger::before, .badge.bg-secondary::before, .badge.bg-info::before, .badge.bg-primary::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; opacity: .85; }

        /* ---------- Alerts ---------- */
        .alert { border: 1px solid transparent; border-radius: 12px; font-size: .85rem; padding: .85rem 1rem; display: flex; gap: .65rem; align-items: flex-start; }
        .alert > .bi { font-size: 1.05rem; line-height: 1.3; }
        .alert-success { background: #ecf7f1; border-color: #cfe9da; color: #1d6a45; }
        .alert-danger { background: #fdf0ef; border-color: #f4d3d0; color: #9d302a; }
        .alert-warning { background: #fdf6e6; border-color: #f3e2b8; color: #7f5810; }
        .alert-info { background: #eef6fb; border-color: #d3e7f3; color: #245f80; }

        /* ---------- Modals & dropdowns ---------- */
        .modal-content { border: none; border-radius: 16px; box-shadow: 0 24px 64px rgba(18,48,42,.22); }
        .modal-header { border-bottom: 1px solid var(--fy-border); padding: 1.1rem 1.4rem; }
        .modal-title { font-size: 1rem; font-weight: 600; }
        .modal-body { padding: 1.4rem; }
        .modal-footer { border-top: 1px solid var(--fy-border); padding: .9rem 1.4rem; background: #fafbfa; border-radius: 0 0 16px 16px; }
        .modal-backdrop.show { opacity: .4; }
        .dropdown-menu { border: 1px solid var(--fy-border); border-radius: 12px; box-shadow: 0 12px 32px rgba(18,48,42,.12); padding: .4rem; font-size: .84rem; }
        .dropdown-item { border-radius: 8px; padding: .5rem .75rem; display: flex; align-items: center; gap: .55rem; color: var(--fy-ink); }
        .dropdown-item:hover { background: #f3f6f5; }
        .dropdown-item.text-danger:hover { background: #fdf1f0; }
        .dropdown-item:active { background: var(--fy-teal-50); color: var(--fy-teal-700); }

        /* ---------- Pagination ---------- */
        .pagination { gap: 4px; margin-bottom: 0; --bs-pagination-font-size: .8rem; }
        .page-link { border: 1px solid var(--fy-border); border-radius: 8px !important; color: #3d4b47; min-width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; padding: 0 .6rem; background: #fff; }
        .page-link:hover { background: #f3f6f5; color: var(--fy-ink); border-color: var(--fy-border-strong); }
        .page-link:focus { box-shadow: 0 0 0 3px rgba(47,122,108,.14); }
        .page-item.active .page-link { background: var(--fy-teal-500); border-color: var(--fy-teal-500); color: #fff; }
        .page-item.disabled .page-link { background: #fafbfa; color: #b3bcb9; }
        nav[role="navigation"] > .d-flex, nav > .d-sm-flex { align-items: center; }
        nav .small.text-muted, nav p.small { margin: 0; }

        /* ---------- Misc ---------- */
        .nav-tabs { border-bottom: 1px solid var(--fy-border); gap: .25rem; }
        .nav-tabs .nav-link { border: none; border-bottom: 2px solid transparent; color: var(--fy-muted); font-weight: 500; padding: .65rem .9rem; }
        .nav-tabs .nav-link:hover { color: var(--fy-ink); border-bottom-color: var(--fy-border-strong); }
        .nav-tabs .nav-link.active { color: var(--fy-teal-600); background: transparent; border-bottom-color: var(--fy-teal-500); }
        .list-group-item { border-color: var(--fy-border); font-size: .85rem; }
        .progress { background: #eef1ef; border-radius: var(--fy-radius-pill); height: .5rem; }
        .progress-bar { background: var(--fy-teal-500); }
        hr { border-color: var(--fy-border); opacity: 1; }
        dt { font-weight: 500; color: var(--fy-muted); font-size: .8rem; }
        code { color: #a3456b; font-size: .8em; }

        /* ---------- Responsive ---------- */
        @media (max-width: 991.98px) {
            .sidebar { position: fixed; left: 0; top: 0; bottom: 0; transform: translateX(-100%); transition: transform .25s ease; box-shadow: 0 0 40px rgba(0,0,0,.25); }
            body.sidebar-open .sidebar { transform: translateX(0); }
            body.sidebar-open .sidebar-backdrop { display: block; position: fixed; inset: 0; background: rgba(10,25,22,.45); z-index: 1030; }
            .fy-menu-btn { display: inline-flex; align-items: center; justify-content: center; }
            .fy-topbar { padding: 0 1rem; }
            .fy-content { padding: 1.25rem 1rem 2rem; }
            .fy-user-btn .name, .fy-user-btn .role, .fy-user-btn .bi-chevron-down { display: none; }
        }
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
            ['route' => 'admin.cms-contents.index', 'match' => 'admin.cms-contents.*', 'icon' => 'bi-file-earmark-text', 'label' => 'CMS Content', 'can' => 'manage-content'],
        ],
        'System' => [
            ['route' => 'admin.settings.index', 'match' => 'admin.settings.*', 'icon' => 'bi-gear', 'label' => 'Settings', 'can' => 'manage-settings'],
            ['route' => 'admin.activity-logs.index', 'match' => 'admin.activity-logs.*', 'icon' => 'bi-clock-history', 'label' => 'Activity Logs', 'can' => 'view-activity-logs'],
        ],
    ];

    $visibleGroups = [];
    foreach ($navGroups as $groupLabel => $items) {
        $visibleItems = array_values(array_filter($items, fn ($item) => $routeExists($item['route']) && $can($item['can'] ?? null)));
        if (! empty($visibleItems)) {
            $visibleGroups[$groupLabel] = $visibleItems;
        }
    }

    $initials = collect(preg_split('/\s+/', trim($user?->name ?? 'Admin')))
        ->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
    $roleName = $user && method_exists($user, 'getRoleNames') ? $user->getRoleNames()->first() : null;
@endphp
<div class="fy-shell">
    <nav class="sidebar" id="fySidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <span class="brand-mark"><i class="bi bi-flower2"></i></span>
            <span>
                <span class="brand-name d-block">FYRIRHYGGJA</span>
                <span class="brand-sub d-block">Admin Console</span>
            </span>
        </a>
        <div class="sidebar-scroll">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2 me-2"></i>Dashboard</a></li>

                @foreach ($visibleGroups as $groupLabel => $items)
                    <li class="nav-header">{{ $groupLabel }}</li>
                    @if (count($items) > 6)
                        @php $collapseId = 'nav-collapse-' . \Illuminate\Support\Str::slug($groupLabel); @endphp
                        @php $groupActive = collect($items)->contains(fn ($item) => request()->routeIs($item['match'])); @endphp
                        <li class="nav-item">
                            <a class="nav-link nav-toggle" data-bs-toggle="collapse" href="#{{ $collapseId }}" role="button" aria-expanded="{{ $groupActive ? 'true' : 'false' }}">
                                <i class="bi bi-collection me-2"></i>
                                <span class="flex-grow-1">{{ $groupLabel }}</span>
                                <i class="bi bi-chevron-down"></i>
                            </a>
                            <div class="collapse {{ $groupActive ? 'show' : '' }}" id="{{ $collapseId }}">
                                <ul class="nav flex-column">
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
        </div>
    </nav>
    <div class="sidebar-backdrop" data-sidebar-close></div>

    <main class="fy-main">
        <header class="fy-topbar">
            <button type="button" class="fy-menu-btn" data-sidebar-toggle aria-label="Toggle navigation"><i class="bi bi-list"></i></button>
            <div class="min-w-0">
                <div class="crumbs"><a href="{{ route('admin.dashboard') }}">Admin</a> <i class="bi bi-chevron-right mx-1" style="font-size:.6rem"></i> @yield('title', 'Dashboard')</div>
                <h1 class="page-title text-truncate">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="ms-auto dropdown">
                <button class="fy-user-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="fy-avatar">{{ $initials }}</span>
                    <span>
                        <span class="name d-block">{{ $user?->name }}</span>
                        <span class="role d-block">{{ $roleName ? \Illuminate\Support\Str::headline($roleName) : 'Administrator' }}</span>
                    </span>
                    <i class="bi bi-chevron-down text-muted" style="font-size:.7rem"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end mt-2" style="min-width: 220px">
                    <li class="px-3 py-2">
                        <div class="fw-semibold small">{{ $user?->name }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $user?->email }}</div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <div class="fy-content">
            @if (session('status'))
                <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i><div>{{ session('status') }}</div></div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger"><i class="bi bi-exclamation-octagon-fill"></i><div>{{ session('error') }}</div></div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                    <ul class="mb-0 ps-3">
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
<script>
    (function () {
        document.querySelectorAll('[data-sidebar-toggle]').forEach((btn) => btn.addEventListener('click', () => document.body.classList.toggle('sidebar-open')));
        document.querySelectorAll('[data-sidebar-close]').forEach((el) => el.addEventListener('click', () => document.body.classList.remove('sidebar-open')));
        if (window.Chart) {
            Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
            Chart.defaults.font.size = 12;
            Chart.defaults.color = '#6b7a76';
        }
    })();
</script>
@stack('scripts')
</body>
</html>
