@extends('admin::layouts.app')

@section('title', 'Subscription Plans')

@section('content')
    @include('admin::components.page-header', ['title' => 'Subscription Plans', 'subtitle' => 'Plans customers can subscribe to.'])

    <div class="card mb-3">
        <div class="card-header fw-semibold">Add plan</div>
        <div class="card-body">
            <div class="row g-2 align-items-end" id="planCreateForm">
                <div class="col-md-2">
                    <label class="form-label small text-muted">Code</label>
                    <input type="text" class="form-control form-control-sm sp-code" placeholder="e.g. PREMIUM">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Name</label>
                    <input type="text" class="form-control form-control-sm sp-name">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Price</label>
                    <input type="number" step="0.01" min="0" class="form-control form-control-sm sp-price">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Currency</label>
                    <input type="text" maxlength="3" class="form-control form-control-sm sp-currency" value="ISK">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Billing interval</label>
                    <select class="form-select form-select-sm sp-interval">
                        @foreach ($billingIntervals as $interval)
                            <option value="{{ $interval }}">{{ ucfirst($interval) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-primary w-100" id="btnAddPlan">Add</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle" id="plansTable" data-store-url="{{ route('admin.subscription-plans.store') }}">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Currency</th>
                            <th>Discount %</th>
                            <th>Interval</th>
                            <th style="width: 100px">Active</th>
                            <th style="width: 170px" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr data-id="{{ $item->id }}">
                                <td><input type="text" class="form-control form-control-sm sp-code" value="{{ $item->code }}"></td>
                                <td><input type="text" class="form-control form-control-sm sp-name" value="{{ $item->name }}"></td>
                                <td><input type="number" step="0.01" min="0" class="form-control form-control-sm sp-price" value="{{ $item->price }}"></td>
                                <td><input type="text" maxlength="3" class="form-control form-control-sm sp-currency" value="{{ $item->currency }}"></td>
                                <td><input type="number" min="0" max="100" class="form-control form-control-sm sp-discount" value="{{ $item->discount_percent }}"></td>
                                <td>
                                    <select class="form-select form-select-sm sp-interval">
                                        @foreach ($billingIntervals as $interval)
                                            <option value="{{ $interval }}" {{ $item->billing_interval === $interval ? 'selected' : '' }}>{{ ucfirst($interval) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input sp-toggle-active" type="checkbox" {{ $item->is_active ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary sp-save">Save</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger sp-delete">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No plans yet.</td></tr>
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
    const table = document.getElementById('plansTable');
    const storeUrl = table.dataset.storeUrl;

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

    function rowPayload(scope) {
        return {
            code: scope.querySelector('.sp-code').value,
            name: scope.querySelector('.sp-name').value,
            price: scope.querySelector('.sp-price').value,
            currency: scope.querySelector('.sp-currency').value,
            discount_percent: scope.querySelector('.sp-discount')?.value ?? 0,
            billing_interval: scope.querySelector('.sp-interval').value,
        };
    }

    document.getElementById('btnAddPlan').addEventListener('click', function () {
        const scope = document.getElementById('planCreateForm');
        const payload = Object.assign(rowPayload(scope), { is_active: true });
        send('POST', storeUrl, payload)
            .then(() => { showToast('Plan added.', 'success'); location.reload(); })
            .catch((err) => showToast(err.message, 'error'));
    });

    table.addEventListener('click', function (e) {
        const btn = e.target.closest('button');
        if (!btn) return;
        const row = btn.closest('tr');
        const id = row.dataset.id;

        if (btn.classList.contains('sp-save')) {
            const payload = Object.assign(rowPayload(row), { is_active: row.querySelector('.sp-toggle-active').checked });
            send('PUT', storeUrl + '/' + id, payload)
                .then(() => showToast('Saved.', 'success'))
                .catch((err) => showToast(err.message, 'error'));
        }

        if (btn.classList.contains('sp-delete')) {
            if (!confirm('Delete this plan?')) return;
            send('DELETE', storeUrl + '/' + id)
                .then(() => { showToast('Deleted.', 'success'); row.remove(); })
                .catch((err) => showToast(err.message, 'error'));
        }
    });

    table.addEventListener('change', function (e) {
        if (!e.target.classList.contains('sp-toggle-active')) return;
        const row = e.target.closest('tr');
        send('POST', storeUrl + '/' + row.dataset.id + '/toggle-active')
            .then((res) => showToast(res.msg, 'success'))
            .catch((err) => showToast(err.message, 'error'));
    });
})();
</script>
@endpush
