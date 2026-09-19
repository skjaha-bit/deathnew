@extends('admin::layouts.app')

@section('title', 'Settings')

@section('content')
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf @method('PUT')
        @foreach ($settings as $group => $items)
            <div class="card p-3 mb-3">
                <h6 class="mb-3 text-capitalize">{{ $group }}</h6>
                @foreach ($items as $setting)
                    <div class="mb-3">
                        <label class="form-label">{{ $setting->key }}</label>
                        <input type="text" name="settings[{{ $setting->key }}]" class="form-control" value="{{ $setting->value }}">
                    </div>
                @endforeach
            </div>
        @endforeach
        <button class="btn btn-primary">Save Settings</button>
    </form>
@endsection
