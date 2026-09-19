{{--
    Usage (from a thin per-model wrapper view):
    @include('admin::components.reference-data-table', [
        'items' => $items, 'routeName' => $routeName, 'extraColumns' => $extraColumns,
    ])
--}}
@php $extraColumns = $extraColumns ?? []; @endphp

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-md-4">
                <label class="form-label small text-muted">Search by name</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm">
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-outline-secondary" type="submit">Search</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle" id="refDataTable" data-store-url="{{ route($routeName.'.store') }}">
                <thead>
                    <tr>
                        <th style="width: 30%">Name</th>
                        @foreach ($extraColumns as $field => $label)
                            <th>{{ $label }}</th>
                        @endforeach
                        <th style="width: 100px">Active</th>
                        <th style="width: 170px" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr data-id="" class="table-light">
                        <td><input type="text" class="form-control form-control-sm rd-name" placeholder="New name..."></td>
                        @foreach ($extraColumns as $field => $label)
                            <td><input type="text" class="form-control form-control-sm rd-extra" data-field="{{ $field }}" placeholder="{{ $label }}"></td>
                        @endforeach
                        <td>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input rd-active" type="checkbox" checked>
                            </div>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-primary rd-add">Add</button>
                        </td>
                    </tr>

                    @forelse ($items as $item)
                        <tr data-id="{{ $item->id }}">
                            <td><input type="text" class="form-control form-control-sm rd-name" value="{{ $item->name }}"></td>
                            @foreach ($extraColumns as $field => $label)
                                <td><input type="text" class="form-control form-control-sm rd-extra" data-field="{{ $field }}" value="{{ $item->{$field} }}"></td>
                            @endforeach
                            <td>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input rd-toggle-active" type="checkbox" {{ $item->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary rd-save">Save</button>
                                <button type="button" class="btn btn-sm btn-outline-danger rd-delete">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ 3 + count($extraColumns) }}" class="text-center text-muted py-4">No records yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $items->links() }}
    </div>
</div>

@push('scripts')
<script>
(function () {
    const table = document.getElementById('refDataTable');
    if (!table || table.dataset.wired) return;
    table.dataset.wired = '1';

    const storeUrl = table.dataset.storeUrl;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    function collectExtra(row) {
        const extra = {};
        row.querySelectorAll('.rd-extra').forEach((input) => { extra[input.dataset.field] = input.value; });
        return extra;
    }

    function send(method, url, body) {
        return fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
            body: body ? JSON.stringify(body) : undefined,
        }).then(async (r) => {
            const data = await r.json().catch(() => ({}));
            if (!r.ok) {
                const message = data.message || Object.values(data.errors || {}).flat()[0] || 'Request failed.';
                throw new Error(message);
            }
            return data;
        });
    }

    table.addEventListener('click', function (e) {
        const btn = e.target.closest('button');
        if (!btn) return;
        const row = btn.closest('tr');

        if (btn.classList.contains('rd-add')) {
            const name = row.querySelector('.rd-name').value.trim();
            if (!name) { showToast('Name is required.', 'error'); return; }
            const payload = Object.assign({ name, is_active: row.querySelector('.rd-active').checked }, collectExtra(row));
            send('POST', storeUrl, payload)
                .then(() => { showToast('Added.', 'success'); location.reload(); })
                .catch((err) => showToast(err.message, 'error'));
        }

        if (btn.classList.contains('rd-save')) {
            const id = row.dataset.id;
            const name = row.querySelector('.rd-name').value.trim();
            const payload = Object.assign({ name, is_active: row.querySelector('.rd-toggle-active').checked }, collectExtra(row));
            send('PUT', storeUrl + '/' + id, payload)
                .then(() => showToast('Saved.', 'success'))
                .catch((err) => showToast(err.message, 'error'));
        }

        if (btn.classList.contains('rd-delete')) {
            const id = row.dataset.id;
            if (!confirm('Delete this record?')) return;
            send('DELETE', storeUrl + '/' + id)
                .then(() => { showToast('Deleted.', 'success'); row.remove(); })
                .catch((err) => showToast(err.message, 'error'));
        }
    });

    table.addEventListener('change', function (e) {
        if (!e.target.classList.contains('rd-toggle-active')) return;
        const row = e.target.closest('tr');
        const id = row.dataset.id;
        if (!id) return;
        send('POST', storeUrl + '/' + id + '/toggle-active')
            .then((res) => showToast(res.msg, 'success'))
            .catch((err) => showToast(err.message, 'error'));
    });
})();
</script>
@endpush
