@extends('admin::layouts.app')

@section('title', 'CMS Content')

@section('content')
    @include('admin::components.page-header', ['title' => 'CMS Content', 'subtitle' => 'Pages, help panels, FAQs and education articles shown in the mobile app.'])

    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#contentModal" id="btnAddContent">
            <i class="bi bi-plus-lg me-1"></i>Add content
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle" id="contentsTable" data-store-url="{{ route('admin.cms-contents.store') }}">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Module key</th>
                            <th>Title (EN)</th>
                            <th style="width: 90px">Sort</th>
                            <th style="width: 100px">Active</th>
                            <th style="width: 170px" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr data-id="{{ $item->id }}"
                                data-type="{{ $item->type }}"
                                data-module-key="{{ $item->module_key }}"
                                data-sort-order="{{ $item->sort_order }}"
                                data-is-active="{{ $item->is_active ? '1' : '0' }}"
                                data-title="{{ json_encode($item->title) }}"
                                data-body="{{ json_encode($item->body) }}">
                                <td><span class="badge bg-secondary">{{ $item->type }}</span></td>
                                <td>{{ $item->module_key }}</td>
                                <td>{{ $item->localized('title', 'en') }}</td>
                                <td>{{ $item->sort_order }}</td>
                                <td>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input cc-toggle-active" type="checkbox" {{ $item->is_active ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary cc-edit" data-bs-toggle="modal" data-bs-target="#contentModal">Edit</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger cc-delete">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No content yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $items->links() }}
        </div>
    </div>

    <div class="modal fade" id="contentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="contentForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="contentModalTitle">Add content</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" value="">
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Type</label>
                                <select name="type" class="form-select form-select-sm" required>
                                    @foreach ($types as $type)
                                        <option value="{{ $type }}">{{ ucwords(str_replace('_', ' ', $type)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Module key</label>
                                <input type="text" name="module_key" class="form-control form-control-sm" placeholder="e.g. insurance" required>
                            </div>
                        </div>

                        @foreach ($languages as $language)
                            <div class="mb-3 border rounded p-2">
                                <div class="fw-semibold small mb-2">{{ $language->name }} ({{ $language->code }})</div>
                                <label class="form-label small text-muted">Title</label>
                                <input type="text" class="form-control form-control-sm mb-2 cc-title" data-locale="{{ $language->code }}" {{ $language->is_default ? 'required' : '' }}>
                                <label class="form-label small text-muted">Body</label>
                                <textarea class="form-control form-control-sm cc-body" data-locale="{{ $language->code }}" rows="4" {{ $language->is_default ? 'required' : '' }}></textarea>
                            </div>
                        @endforeach

                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Sort order</label>
                                <input type="number" name="sort_order" class="form-control form-control-sm" value="0" min="0">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="ccIsActive" checked>
                                    <label class="form-check-label small" for="ccIsActive">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const table = document.getElementById('contentsTable');
    const storeUrl = table.dataset.storeUrl;
    const form = document.getElementById('contentForm');
    const modalTitle = document.getElementById('contentModalTitle');

    async function send(method, url, payload) {
        const r = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
            body: JSON.stringify(payload),
        });
        const data = await r.json().catch(() => ({}));
        if (!r.ok) {
            const message = data.message || Object.values(data.errors || {}).flat()[0] || 'Request failed.';
            throw new Error(message);
        }
        return data;
    }

    function resetForm() {
        form.reset();
        form.querySelector('[name="id"]').value = '';
        modalTitle.textContent = 'Add content';
    }

    document.getElementById('btnAddContent').addEventListener('click', resetForm);

    table.addEventListener('click', function (e) {
        const btn = e.target.closest('button');
        if (!btn) return;
        const row = btn.closest('tr');
        const id = row.dataset.id;

        if (btn.classList.contains('cc-edit')) {
            resetForm();
            modalTitle.textContent = 'Edit content';
            form.querySelector('[name="id"]').value = id;
            form.querySelector('[name="type"]').value = row.dataset.type;
            form.querySelector('[name="module_key"]').value = row.dataset.moduleKey;
            form.querySelector('[name="sort_order"]').value = row.dataset.sortOrder;
            form.querySelector('[name="is_active"]').checked = row.dataset.isActive === '1';
            const title = JSON.parse(row.dataset.title || '{}');
            const body = JSON.parse(row.dataset.body || '{}');
            form.querySelectorAll('.cc-title').forEach((el) => { el.value = title[el.dataset.locale] || ''; });
            form.querySelectorAll('.cc-body').forEach((el) => { el.value = body[el.dataset.locale] || ''; });
        }

        if (btn.classList.contains('cc-delete')) {
            if (!confirm('Delete this content item?')) return;
            send('DELETE', storeUrl + '/' + id)
                .then(() => { showToast('Deleted.', 'success'); row.remove(); })
                .catch((err) => showToast(err.message, 'error'));
        }
    });

    table.addEventListener('change', function (e) {
        if (!e.target.classList.contains('cc-toggle-active')) return;
        const row = e.target.closest('tr');
        send('POST', storeUrl + '/' + row.dataset.id + '/toggle-active')
            .then((res) => showToast(res.msg, 'success'))
            .catch((err) => showToast(err.message, 'error'));
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const id = form.querySelector('[name="id"]').value;
        const title = {};
        const body = {};
        form.querySelectorAll('.cc-title').forEach((el) => { title[el.dataset.locale] = el.value; });
        form.querySelectorAll('.cc-body').forEach((el) => { body[el.dataset.locale] = el.value; });

        const payload = {
            type: form.querySelector('[name="type"]').value,
            module_key: form.querySelector('[name="module_key"]').value,
            sort_order: form.querySelector('[name="sort_order"]').value,
            is_active: form.querySelector('[name="is_active"]').checked,
            title,
            body,
        };

        const request = id ? send('PUT', storeUrl + '/' + id, payload) : send('POST', storeUrl, payload);
        request
            .then(() => { showToast('Saved.', 'success'); location.reload(); })
            .catch((err) => showToast(err.message, 'error'));
    });
})();
</script>
@endpush
