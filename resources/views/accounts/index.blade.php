@extends('layouts.app')

@section('title', 'Accounts')

@php
$typeConfig = [
    'Debit'   => ['label' => 'Debit',   'icon' => 'ti-cash',        'bg' => '#DBEAFE', 'color' => '#1D4ED8', 'accent' => '#3B82F6'],
    'Savings' => ['label' => 'Savings', 'icon' => 'ti-wallet',      'bg' => '#D1FAE5', 'color' => '#065F46', 'accent' => '#10B981'],
    'Credit'  => ['label' => 'Credit',  'icon' => 'ti-receipt',     'bg' => '#FEE2E2', 'color' => '#991B1B', 'accent' => '#EF4444'],
    'Lent'    => ['label' => 'Lent',    'icon' => 'ti-arrow-right', 'bg' => '#FEF3C7', 'color' => '#92400E', 'accent' => '#F59E0B'],
];
@endphp

@section('content')

{{-- ── Page header ────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-0" style="font-size:1.25rem;color:#0F172A;">Accounts</h2>
        <p class="text-muted mb-0" style="font-size:.8rem;">{{ $accounts->count() }} {{ Str::plural('account', $accounts->count()) }}</p>
    </div>
    <button class="btn btn-primary btn-sm d-flex align-items-center gap-1"
            data-bs-toggle="modal" data-bs-target="#addAccountModal">
        <i class="ti ti-plus"></i> Add Account
    </button>
</div>

{{-- ── Net Worth card ──────────────────────────────────────────────── --}}
<div class="nw-card mb-4">
    <div class="nw-main">
        <div class="nw-label">NET WORTH</div>
        <div class="nw-value {{ $netWorth >= 0 ? 'nw-positive' : 'nw-negative' }}">
            {{ $netWorth >= 0 ? '' : '-' }}₱{{ number_format(abs($netWorth), 2) }}
        </div>
    </div>
    <div class="nw-breakdown">
        <div class="nw-side">
            <div class="nw-side-label"><i class="ti ti-trending-up me-1"></i>ASSETS</div>
            <div class="nw-side-value" style="color:#10B981;">₱{{ number_format($assets, 2) }}</div>
        </div>
        <div class="nw-divider"></div>
        <div class="nw-side">
            <div class="nw-side-label"><i class="ti ti-trending-down me-1"></i>LIABILITIES</div>
            <div class="nw-side-value" style="color:#EF4444;">₱{{ number_format($liabilities, 2) }}</div>
        </div>
    </div>
</div>

{{-- ── Account groups ──────────────────────────────────────────────── --}}
@foreach (['Debit','Savings','Credit','Lent'] as $type)
    @php $group = $grouped->get($type, collect()); @endphp
    @if ($group->isNotEmpty())
        @php $cfg = $typeConfig[$type]; @endphp
        <div class="mb-4">
            <div class="acct-group-header">
                <div class="acct-group-icon" style="background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};">
                    <i class="ti {{ $cfg['icon'] }}"></i>
                </div>
                <span class="acct-group-label">{{ $cfg['label'] }}</span>
                <span class="acct-group-total">₱{{ number_format($group->sum('balance'), 2) }}</span>
            </div>

            <div class="row g-3 mt-1">
                @foreach ($group as $account)
                    <div class="col-12 col-sm-6 col-lg-4" id="acct-{{ $account->id }}">
                        <div class="acct-card" style="border-top:3px solid {{ $cfg['accent'] }};">
                            <div class="acct-card-top">
                                <div class="acct-icon" style="background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};">
                                    <i class="ti {{ $cfg['icon'] }}"></i>
                                </div>
                                <div class="acct-info">
                                    <div class="acct-name">{{ $account->name }}</div>
                                    <span class="acct-type-badge" style="background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};">
                                        {{ $type }}
                                    </span>
                                </div>
                                <div class="acct-actions">
                                    <button class="btn btn-sm btn-action btn-action-edit me-1" title="Edit"
                                            onclick="openEditAccountModal({{ $account->id }}, '{{ addslashes($account->name) }}', '{{ $account->type }}', '{{ $account->balance }}', '{{ $account->available ?? '' }}', '{{ $account->due_date?->format('Y-m-d') ?? '' }}', '{{ addslashes($account->notes ?? '') }}')">
                                        <i class="ti ti-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-action btn-action-delete" title="Delete"
                                            onclick="openDeleteAccountModal({{ $account->id }}, '{{ addslashes($account->name) }}')">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="acct-balance">₱{{ number_format($account->balance, 2) }}</div>

                            @if ($account->available !== null)
                                <div class="acct-meta">Available: ₱{{ number_format($account->available, 2) }}</div>
                            @endif
                            @if ($account->due_date)
                                <div class="acct-meta"><i class="ti ti-calendar me-1"></i>Due: {{ $account->due_date->format('M d, Y') }}</div>
                            @endif
                            @if ($account->notes)
                                <div class="acct-notes">{{ $account->notes }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endforeach

{{-- Empty state --}}
@if ($accounts->isEmpty())
    <div class="app-card p-5 text-center">
        <div style="width:64px;height:64px;border-radius:16px;background:#EEF3EB;color:#7B9669;display:flex;align-items:center;justify-content:center;font-size:1.75rem;margin:0 auto;">
            <i class="ti ti-wallet"></i>
        </div>
        <h5 class="mt-3 fw-bold" style="color:#1F2937;">No accounts yet</h5>
        <p class="text-muted" style="font-size:.85rem;">Add your debit cards, savings, credit cards, and loans to track your net worth.</p>
        <button class="btn btn-primary btn-sm px-4 mt-2"
                data-bs-toggle="modal" data-bs-target="#addAccountModal">
            <i class="ti ti-plus me-1"></i> Add First Account
        </button>
    </div>
@endif


{{-- ═══════════════════════════════════════════════════════════════
     MODAL — ADD ACCOUNT
═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="addAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content modal-custom">
            <div class="modal-header-custom">
                <h5 class="modal-title-custom"><i class="ti ti-circle-plus me-2"></i>Add Account</h5>
                <button type="button" class="modal-close" data-bs-dismiss="modal"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{ route('accounts.store') }}" method="POST">
                @csrf
                <div class="modal-body-custom">
                    @include('accounts._form')
                </div>
                <div class="modal-footer-custom">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="ti ti-circle-plus me-1"></i> Add Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     MODAL — EDIT ACCOUNT
═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content modal-custom">
            <div class="modal-header-custom">
                <h5 class="modal-title-custom"><i class="ti ti-pencil me-2"></i>Edit Account</h5>
                <button type="button" class="modal-close" data-bs-dismiss="modal"><i class="ti ti-x"></i></button>
            </div>
            <form id="editAccountForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body-custom">
                    @include('accounts._form', ['edit' => true])
                </div>
                <div class="modal-footer-custom">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="ti ti-device-floppy me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form id="deleteAccountForm" method="POST">
                @csrf @method('DELETE')
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <div class="mb-3">
                        <i class="ti ti-alert-triangle" style="font-size:3rem;color:#EF4444;opacity:.75;"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Delete Account?</h5>
                    <p class="text-muted mb-0">
                        You are about to delete <strong id="deleteAccountName"></strong>.
                        This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="ti ti-trash me-1"></i>Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* ── Net worth card ── */
    .nw-card {
        background: linear-gradient(135deg, #404E3B 0%, #5a7052 100%);
        border-radius: 14px;
        padding: 1.5rem 2rem;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1.5rem;
        box-shadow: 0 4px 20px rgba(64,78,59,.3);
    }
    .nw-label { font-size: .7rem; font-weight: 700; letter-spacing: .1em; opacity: .7; margin-bottom: .2rem; }
    .nw-value { font-size: 2rem; font-weight: 800; letter-spacing: -.02em; }
    .nw-positive { color: #fff; }
    .nw-negative { color: #FCA5A5; }
    .nw-breakdown { display: flex; align-items: center; gap: 1.5rem; }
    .nw-divider { width: 1px; height: 40px; background: rgba(255,255,255,.2); }
    .nw-side-label { font-size: .65rem; font-weight: 700; letter-spacing: .08em; opacity: .7; margin-bottom: .2rem; }
    .nw-side-value { font-size: 1.1rem; font-weight: 700; }

    /* ── Group header ── */
    .acct-group-header {
        display: flex;
        align-items: center;
        gap: .65rem;
        padding: .5rem 0;
        border-bottom: 1px solid #E5E7EB;
        margin-bottom: .25rem;
    }
    .acct-group-icon {
        width: 30px; height: 30px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: .9rem; flex-shrink: 0;
    }
    .acct-group-label { font-size: .8rem; font-weight: 700; color: #374151; flex: 1; text-transform: uppercase; letter-spacing: .06em; }
    .acct-group-total { font-size: .85rem; font-weight: 700; color: #1F2937; }

    /* ── Account card ── */
    .acct-card {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 10px;
        padding: 1.1rem 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        transition: box-shadow .2s, transform .2s;
        height: 100%;
    }
    .acct-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.1); transform: translateY(-2px); }

    .acct-card-top { display: flex; align-items: flex-start; gap: .75rem; margin-bottom: .75rem; }
    .acct-icon {
        width: 38px; height: 38px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; flex-shrink: 0;
    }
    .acct-info { flex: 1; min-width: 0; }
    .acct-name { font-size: .9rem; font-weight: 600; color: #1F2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .acct-type-badge { display: inline-block; padding: .1rem .45rem; border-radius: 5px; font-size: .65rem; font-weight: 700; margin-top: .2rem; letter-spacing: .03em; }
    .acct-actions { display: flex; gap: .2rem; flex-shrink: 0; }
    .acct-balance { font-size: 1.4rem; font-weight: 800; color: #1F2937; letter-spacing: -.02em; margin-bottom: .35rem; }
    .acct-meta { font-size: .75rem; color: #6B7280; margin-bottom: .2rem; }
    .acct-notes { font-size: .75rem; color: #9CA3AF; margin-top: .35rem; border-top: 1px solid #F1F5F9; padding-top: .35rem; }

    /* ── Extra field toggle ── */
    .acct-extra { border-top: 1px solid #E5E7EB; margin-top: .75rem; padding-top: .75rem; }
</style>
@endpush

@push('scripts')
<script>
    function openEditAccountModal(id, name, type, balance, available, dueDate, notes) {
        const form = document.getElementById('editAccountForm');
        form.action = '/accounts/' + id;
        form.querySelector('[name="name"]').value      = name;
        form.querySelector('[name="type"]').value      = type;
        form.querySelector('[name="balance"]').value   = balance;
        form.querySelector('[name="available"]').value = available;
        form.querySelector('[name="due_date"]').value  = dueDate;
        form.querySelector('[name="notes"]').value     = notes;
        toggleExtraFields(form.querySelector('[name="type"]'));
        new bootstrap.Modal(document.getElementById('editAccountModal')).show();
    }

    function toggleExtraFields(select) {
        const form    = select.closest('form');
        const extra   = form.querySelector('.acct-extra');
        const avlWrap = form.querySelector('.avl-wrap');
        const dueWrap = form.querySelector('.due-wrap');
        const type    = select.value;

        extra.style.display   = (type === 'Credit' || type === 'Lent') ? '' : 'none';
        avlWrap.style.display = type === 'Credit' ? '' : 'none';
        dueWrap.style.display = (type === 'Credit' || type === 'Lent') ? '' : 'none';
    }

    // Init both forms on page load
    document.querySelectorAll('[name="type"]').forEach(sel => toggleExtraFields(sel));

    function openDeleteAccountModal(id, name) {
        document.getElementById('deleteAccountForm').action = '/accounts/' + id;
        document.getElementById('deleteAccountName').textContent = name;
        new bootstrap.Modal(document.getElementById('deleteAccountModal')).show();
    }
</script>
@endpush
