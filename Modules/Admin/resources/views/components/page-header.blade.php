{{-- Usage: @include('admin::components.page-header', ['title' => '...', 'subtitle' => '...', 'actionsView' => 'module::admin.x._actions']) --}}
<div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-4">
    <div>
        <h4 class="mb-0 fw-bold">{{ $title }}</h4>
        @isset($subtitle)
            <p class="text-muted mb-0 small">{{ $subtitle }}</p>
        @endisset
    </div>
    @isset($actionsView)
        <div class="d-flex gap-2">
            @include($actionsView)
        </div>
    @endisset
</div>
