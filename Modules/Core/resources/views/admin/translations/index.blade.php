@extends('admin::layouts.app')

@section('title', 'Language Management')

@push('styles')
<style>
    .lang-table th { font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; background: #fafbfc; }
    .lang-table td { vertical-align: middle; padding: 8px 12px; }
    .lang-table textarea.form-control {
        font-size: 13px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 6px 10px;
        resize: none;
        min-height: 38px;
        transition: border-color .15s;
    }
    .lang-table textarea.form-control:focus { border-color: #14342e; box-shadow: 0 0 0 3px rgba(20,52,46,.1); }
    .btn-save-all { border-radius: 8px; font-weight: 600; }
    .row-num { width: 40px; color: #9ca3af; font-size: 13px; text-align: center; }
</style>
@endpush

@section('content')

<div class="card p-3 mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search original text" value="{{ request('search') }}">
        </div>
        <div class="col-md-2"><button class="btn btn-outline-secondary w-100">Filter</button></div>
        <div class="col-md-3 ms-auto text-md-end">
            <a href="{{ route('admin.languages.index') }}" class="btn btn-outline-secondary w-100">
                <i class="bi bi-globe me-1"></i> Manage Languages
            </a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
        <h6 class="mb-0"><i class="bi bi-translate me-2" style="color:#14342e"></i>Language Strings</h6>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" id="btnAddRow" type="button">
                <i class="bi bi-plus-lg me-1"></i> Add Row
            </button>
            <button class="btn btn-success btn-sm btn-save-all" id="btnSaveAll" type="button">
                <i class="bi bi-floppy me-1"></i> Save All
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table lang-table mb-0" id="langTable">
            <thead>
                <tr>
                    <th class="row-num">#</th>
                    <th style="min-width:220px">Original Text</th>
                    @foreach ($languages as $language)
                        <th style="min-width:220px">{{ $language->name }} ({{ strtoupper($language->code) }})</th>
                    @endforeach
                    <th style="width:60px"></th>
                </tr>
            </thead>
            <tbody id="langBody">
                @forelse ($translations as $translation)
                <tr data-id="{{ $translation->id }}">
                    <td class="row-num">{{ $loop->iteration }}</td>
                    <td>
                        <textarea class="form-control" name="key" rows="1">{{ $translation->key }}</textarea>
                    </td>
                    @foreach ($languages as $language)
                        @php $value = $translation->values->firstWhere('language_id', $language->id); @endphp
                        <td>
                            <textarea class="form-control" data-lang="{{ $language->code }}" rows="1">{{ $value?->value }}</textarea>
                        </td>
                    @endforeach
                    <td>
                        <form method="POST" action="{{ route('admin.translations.destroy', $translation) }}" onsubmit="return confirm('Delete this key?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr id="emptyRow">
                    <td colspan="{{ 3 + $languages->count() }}" class="text-center py-4 text-muted">No entries yet — click "Add Row" to create one.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $translations->links() }}</div>

@endsection

@push('scripts')
<script>
const saveAllUrl = "{{ route('admin.translations.save-all') }}";
const csrf = "{{ csrf_token() }}";
const languageCodes = @json($languages->pluck('code'));

function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = el.scrollHeight + 'px';
}
document.querySelectorAll('#langTable textarea.form-control').forEach(t => {
    autoResize(t);
    t.addEventListener('input', () => autoResize(t));
});

document.getElementById('btnAddRow').addEventListener('click', () => {
    document.getElementById('emptyRow')?.remove();
    const tbody = document.getElementById('langBody');
    const tr = document.createElement('tr');
    let cols = `<td class="row-num">*</td><td><textarea class="form-control" name="key" rows="1"></textarea></td>`;
    languageCodes.forEach(code => {
        cols += `<td><textarea class="form-control" data-lang="${code}" rows="1"></textarea></td>`;
    });
    cols += `<td></td>`;
    tr.innerHTML = cols;
    tbody.appendChild(tr);
    tr.querySelectorAll('textarea').forEach(t => t.addEventListener('input', () => autoResize(t)));
});

document.getElementById('btnSaveAll').addEventListener('click', async () => {
    const rows = [];
    document.querySelectorAll('#langBody tr[data-id], #langBody tr:not([data-id]):not(#emptyRow)').forEach(tr => {
        const keyEl = tr.querySelector('[name="key"]');
        if (!keyEl) return;

        const values = {};
        languageCodes.forEach(code => {
            const el = tr.querySelector(`[data-lang="${code}"]`);
            values[code] = el ? el.value.trim() : '';
        });

        rows.push({
            id: tr.dataset.id || '',
            key: keyEl.value.trim(),
            values,
        });
    });

    const btn = document.getElementById('btnSaveAll');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving…';

    try {
        const res = await fetch(saveAllUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ rows }),
        });
        const json = await res.json();
        showToast(json.msg, json.res ? 'success' : 'error');
        if (json.res) setTimeout(() => location.reload(), 800);
    } catch {
        showToast('Something went wrong', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-floppy me-1"></i> Save All';
    }
});
</script>
@endpush
