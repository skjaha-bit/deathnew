@extends('admin::layouts.app')

@section('title', 'Banners')

@section('content')
    @include('admin::components.page-header', ['title' => 'Banners', 'subtitle' => 'Marketing banners shown in the mobile app.'])

    <div class="card mb-3">
        <div class="card-header fw-semibold">Add banner</div>
        <div class="card-body">
            <form id="bannerCreateForm" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Title</label>
                    <input type="text" name="title" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Link URL</label>
                    <input type="url" name="link_url" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Sort order</label>
                    <input type="number" name="sort_order" class="form-control form-control-sm" value="0" min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Image</label>
                    <input type="file" name="image" class="form-control form-control-sm" accept="image/*" required>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100">Add</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle" id="bannersTable" data-store-url="{{ route('admin.banners.store') }}">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Link URL</th>
                            <th style="width: 90px">Sort</th>
                            <th style="width: 100px">Active</th>
                            <th style="width: 170px" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr data-id="{{ $item->id }}">
                                <td><img src="{{ asset('storage/'.$item->image_path) }}" alt="" style="height: 40px; border-radius: 6px;"></td>
                                <td><input type="text" class="form-control form-control-sm bn-title" value="{{ $item->title }}"></td>
                                <td><input type="url" class="form-control form-control-sm bn-link" value="{{ $item->link_url }}"></td>
                                <td><input type="number" class="form-control form-control-sm bn-sort" value="{{ $item->sort_order }}" min="0"></td>
                                <td>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input bn-toggle-active" type="checkbox" {{ $item->is_active ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary bn-save">Save</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger bn-delete">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No banners yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $items->links() }}
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const table = document.getElementById('bannersTable');
    const storeUrl = table.dataset.storeUrl;

    async function send(url, formData) {
        const r = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' }, body: formData });
        const data = await r.json().catch(() => ({}));
        if (!r.ok) {
            const message = data.message || Object.values(data.errors || {}).flat()[0] || 'Request failed.';
            throw new Error(message);
        }
        return data;
    }

    document.getElementById('bannerCreateForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        send(storeUrl, formData)
            .then(() => { showToast('Banner added.', 'success'); location.reload(); })
            .catch((err) => showToast(err.message, 'error'));
    });

    table.addEventListener('click', function (e) {
        const btn = e.target.closest('button');
        if (!btn) return;
        const row = btn.closest('tr');
        const id = row.dataset.id;

        if (btn.classList.contains('bn-save')) {
            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('title', row.querySelector('.bn-title').value);
            formData.append('link_url', row.querySelector('.bn-link').value);
            formData.append('sort_order', row.querySelector('.bn-sort').value);
            formData.append('is_active', row.querySelector('.bn-toggle-active').checked ? '1' : '0');
            send(storeUrl + '/' + id, formData)
                .then(() => showToast('Saved.', 'success'))
                .catch((err) => showToast(err.message, 'error'));
        }

        if (btn.classList.contains('bn-delete')) {
            if (!confirm('Delete this banner?')) return;
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            send(storeUrl + '/' + id, formData)
                .then(() => { showToast('Deleted.', 'success'); row.remove(); })
                .catch((err) => showToast(err.message, 'error'));
        }
    });

    table.addEventListener('change', function (e) {
        if (!e.target.classList.contains('bn-toggle-active')) return;
        const row = e.target.closest('tr');
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('title', row.querySelector('.bn-title').value);
        formData.append('link_url', row.querySelector('.bn-link').value);
        formData.append('sort_order', row.querySelector('.bn-sort').value);
        formData.append('is_active', e.target.checked ? '1' : '0');
        send(storeUrl + '/' + row.dataset.id, formData)
            .then(() => showToast('Saved.', 'success'))
            .catch((err) => showToast(err.message, 'error'));
    });
})();
</script>
@endpush
