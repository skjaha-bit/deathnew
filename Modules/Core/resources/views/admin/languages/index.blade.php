@extends('admin::layouts.app')

@section('title', 'Languages')

@section('content')
    <div class="card p-3 mb-3">
        <h6 class="mb-3">Add Language</h6>
        <form method="POST" action="{{ route('admin.languages.store') }}" class="row g-2">
            @csrf
            <div class="col-md-3">
                <input type="text" name="code" class="form-control" placeholder="Code (e.g. is)" maxlength="5" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Name (e.g. Icelandic)" required>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Add</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light"><tr><th>Code</th><th>Name</th><th>Status</th><th>Default</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @foreach ($languages as $language)
                        <tr>
                            <td>{{ strtoupper($language->code) }}</td>
                            <td>{{ $language->name }}</td>
                            <td><span class="badge bg-{{ $language->is_active ? 'success' : 'secondary' }}">{{ $language->is_active ? 'Active' : 'Disabled' }}</span></td>
                            <td>@if ($language->is_default) <span class="badge bg-primary">Default</span> @endif</td>
                            <td class="text-end d-flex gap-2 justify-content-end">
                                <form method="POST" action="{{ route('admin.languages.toggle-active', $language) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-secondary">{{ $language->is_active ? 'Disable' : 'Enable' }}</button>
                                </form>
                                @unless ($language->is_default)
                                    <form method="POST" action="{{ route('admin.languages.make-default', $language) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-primary">Make Default</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.languages.destroy', $language) }}" onsubmit="return confirm('Delete this language?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
