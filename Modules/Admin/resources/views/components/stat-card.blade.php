{{-- Usage: @include('admin::components.stat-card', ['label' => 'Customers', 'value' => 128, 'icon' => 'bi-people']) --}}
<div class="card stat-card h-100">
    <div class="card-body d-flex align-items-center gap-3">
        @isset($icon)
            <div class="stat-icon"><i class="bi {{ $icon }}"></i></div>
        @endisset
        <div>
            <div class="stat-value">{{ $value }}</div>
            <div class="text-muted small">{{ $label }}</div>
        </div>
    </div>
</div>
