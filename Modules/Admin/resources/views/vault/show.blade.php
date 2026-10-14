@extends('admin::layouts.app')

@section('title', 'Vault: '.$target->name)

@section('content')
    @include('admin::components.page-header', ['title' => $target->name.'\'s Vault', 'subtitle' => 'Access expires '.$grant->access_expires_at->format('Y-m-d H:i').'. Every section view is logged.'])

    <div class="alert alert-warning small">
        <i class="bi bi-shield-lock-fill me-1"></i>
        This vault is unlocked only for you, until it expires. Approved by {{ $grant->approver->name }} on {{ $grant->decided_at->format('Y-m-d H:i') }}.
    </div>

    <div class="card p-3 mb-3">
        <h6 class="mb-3"><i class="bi bi-bank me-1"></i>Commercial Banks</h6>
        @forelse ($bankAccounts as $bank)
            <div class="border rounded p-2 mb-2">
                <div class="fw-semibold">{{ $bank->bank?->name ?? 'Unknown bank' }}</div>
                @foreach ($bank->accounts as $account)
                    <div class="small text-muted">Account: {{ $account->account_number }} · SWIFT/IBAN: {{ $account->swift_iban }}@if ($account->is_joint) · Joint with: {{ $account->joint_holder_name ?? '—' }}@endif</div>
                @endforeach
                @if ($bank->notes_text)<div class="small mt-1">{{ $bank->notes_text }}</div>@endif
            </div>
        @empty
            <p class="text-muted small mb-0">No bank records.</p>
        @endforelse
    </div>

    <div class="card p-3 mb-3">
        <h6 class="mb-3"><i class="bi bi-shield-check me-1"></i>Insurance Policies</h6>
        @forelse ($insurancePolicies as $policy)
            <div class="border rounded p-2 mb-2">
                <div class="fw-semibold">{{ $policy->insuranceCompany?->name ?? $policy->other_type_text }}</div>
                @if ($policy->contact_message)<div class="small text-muted">{{ $policy->contact_message }}</div>@endif
            </div>
        @empty
            <p class="text-muted small mb-0">No insurance records.</p>
        @endforelse
    </div>

    <div class="card p-3 mb-3">
        <h6 class="mb-3"><i class="bi bi-piggy-bank me-1"></i>Pension Entries</h6>
        @forelse ($pensionEntries as $entry)
            <div class="border rounded p-2 mb-2">
                <div class="fw-semibold">{{ $entry->pensionFund?->name ?? $entry->custom_fund_name }}</div>
                <div class="small text-muted">{{ $entry->from_year }}–{{ $entry->to_year ?? 'present' }} · Estimated credit: {{ $entry->estimated_credit }}</div>
                @if ($entry->special_rights_text)<div class="small mt-1">{{ $entry->special_rights_text }}</div>@endif
            </div>
        @empty
            <p class="text-muted small mb-0">No pension records.</p>
        @endforelse
    </div>

    <div class="card p-3 mb-3">
        <h6 class="mb-3"><i class="bi bi-briefcase me-1"></i>Trade Union</h6>
        @forelse ($tradeUnions as $union)
            <div class="border rounded p-2 mb-2">
                <div class="fw-semibold">{{ $union->tradeUnion?->name ?? $union->union_name }}</div>
                @if ($union->rights_text)<div class="small mt-1">{{ $union->rights_text }}</div>@endif
                @if ($union->notes_text)<div class="small text-muted">{{ $union->notes_text }}</div>@endif
            </div>
        @empty
            <p class="text-muted small mb-0">No trade union records.</p>
        @endforelse
    </div>

    <div class="card p-3">
        <h6 class="mb-3"><i class="bi bi-file-earmark-text me-1"></i>Inheritance Matters</h6>
        @if ($inheritanceMatter)
            <div class="small mb-2">Will exists: {{ $inheritanceMatter->will_exists ? 'Yes' : 'No' }} · Location: {{ $inheritanceMatter->will_location ?? '—' }}</div>
            @if ($inheritanceMatter->notes)<div class="small mb-2">{{ $inheritanceMatter->notes }}</div>@endif
            @forelse ($inheritanceMatter->agents as $agent)
                <div class="border rounded p-2 mb-2 small">{{ $agent->name ?? 'Agent' }} — {{ $agent->phone ?? '' }} {{ $agent->address ?? '' }}</div>
            @empty
                <p class="text-muted small mb-0">No named agents.</p>
            @endforelse
        @else
            <p class="text-muted small mb-0">No inheritance matter recorded.</p>
        @endif
    </div>
@endsection
