@extends('layouts.app')

@section('title', 'Transactions')

@section('content')

{{-- ── Page header ───────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-0" style="font-size:1.25rem;color:#0F172A;">Transactions</h2>
        <p class="text-muted mb-0" style="font-size:.8rem;">
            {{ $transactions->count() }} record{{ $transactions->count() !== 1 ? 's' : '' }} found
        </p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        {{-- View toggle --}}
        <div class="view-toggle-group">
            <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}"
               class="view-toggle-btn {{ $view === 'list' ? 'active' : '' }}">
                <i class="ti ti-list"></i> List
            </a>
            <a href="{{ request()->fullUrlWithQuery(['view' => 'calendar']) }}"
               class="view-toggle-btn {{ $view === 'calendar' ? 'active' : '' }}">
                <i class="ti ti-calendar"></i> Calendar
            </a>
        </div>
        {{-- Export --}}
        <a href="{{ route('transactions.export', request()->only(['month','type'])) }}"
           class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="ti ti-download"></i> Export CSV
        </a>
    @if ($categories->isNotEmpty())
        <button class="btn btn-primary btn-sm d-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#addTransactionModal">
            <i class="ti ti-plus"></i> Add Transaction
        </button>
    @else
        <a href="{{ route('budgets.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
            <i class="ti ti-tag"></i> Add Categories First
        </a>
    @endif
    </div>{{-- /d-flex --}}
</div>

{{-- ── Filters ───────────────────────────────────────────────────────── --}}
<div class="app-card p-3 mb-4">
    <form action="{{ route('transactions.index') }}" method="GET">
        <div class="row g-2 align-items-end">

            <div class="col-12 col-sm-6 col-lg-2">
                <label class="filter-label">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="Income"  {{ request('type') === 'Income'  ? 'selected' : '' }}>Income</option>
                    <option value="Expense" {{ request('type') === 'Expense' ? 'selected' : '' }}>Expense</option>
                </select>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <label class="filter-label">Category</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-sm-6 col-lg-2">
                <label class="filter-label">Month</label>
                <select name="month" class="form-select form-select-sm">
                    <option value="">All Months</option>
                    @foreach ($months as $value => $label)
                        <option value="{{ $value }}" {{ request('month') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <label class="filter-label">Search</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search description…"
                       value="{{ request('search') }}">
            </div>

            <div class="col-12 col-lg-2">
                <label class="filter-label d-none d-lg-block">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit"
                            class="btn btn-primary btn-sm flex-grow-1"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Apply filters">
                        <i class="ti ti-filter me-1"></i> Search
                    </button>
                    @if (request()->hasAny(['type','category','month','search']))
                        <a href="{{ route('transactions.index') }}"
                           class="btn btn-outline-secondary btn-sm px-2"
                           data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           title="Clear all filters">
                            <i class="ti ti-x"></i>
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </form>
</div>

{{-- ════════════════════════════════════════════════════════════════
     CALENDAR VIEW
════════════════════════════════════════════════════════════════ --}}
@if ($view === 'calendar')
<div class="cal-wrap app-card p-0 overflow-hidden mb-4">

    {{-- Calendar nav --}}
    <div class="cal-nav">
        <a href="{{ request()->fullUrlWithQuery(['cal_month' => $calMonth->copy()->subMonth()->format('Y-m'), 'view' => 'calendar']) }}"
           class="cal-nav-btn"><i class="ti ti-chevron-left"></i></a>
        <span class="cal-nav-title">{{ $calMonth->format('F Y') }}</span>
        <a href="{{ request()->fullUrlWithQuery(['cal_month' => $calMonth->copy()->addMonth()->format('Y-m'), 'view' => 'calendar']) }}"
           class="cal-nav-btn"><i class="ti ti-chevron-right"></i></a>
    </div>

    {{-- Month summary --}}
    <div class="cal-summary">
        <span><i class="ti ti-trending-up me-1" style="color:#10B981;"></i>Income: <strong style="color:#10B981;">₱{{ number_format($calMonthIncome,2) }}</strong></span>
        <span><i class="ti ti-trending-down me-1" style="color:#EF4444;"></i>Expense: <strong style="color:#EF4444;">₱{{ number_format($calMonthExpense,2) }}</strong></span>
        <span>Balance: <strong style="color:{{ ($calMonthIncome-$calMonthExpense)>=0?'#10B981':'#EF4444' }};">{{ ($calMonthIncome-$calMonthExpense)>=0?'+':'' }}₱{{ number_format(abs($calMonthIncome-$calMonthExpense),2) }}</strong></span>
    </div>

    {{-- Day-of-week headers --}}
    <div class="cal-grid">
        @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $dow)
            <div class="cal-dow">{{ $dow }}</div>
        @endforeach

        {{-- Leading empty cells --}}
        @php $startDow = (int)$calMonth->copy()->startOfMonth()->format('w'); @endphp
        @for($i = 0; $i < $startDow; $i++)
            <div class="cal-day cal-day-empty"></div>
        @endfor

        {{-- Day cells --}}
        @for($d = 1; $d <= $calMonth->daysInMonth; $d++)
            @php
                $dateKey  = $calMonth->format('Y-m-') . str_pad($d, 2, '0', STR_PAD_LEFT);
                $dayData  = $dayMap[$dateKey] ?? null;
                $isToday  = $dateKey === now()->format('Y-m-d');
            @endphp
            <div class="cal-day {{ $isToday ? 'cal-day-today' : '' }} {{ $dayData ? 'cal-day-has-data' : '' }}"
                 data-date="{{ $dateKey }}"
                 onclick="showDayDetail('{{ $dateKey }}')">
                <div class="cal-day-num {{ $isToday ? 'cal-today-num' : '' }}">{{ $d }}</div>
                @if($dayData)
                    @if($dayData['income'] > 0)
                        <div class="cal-amt cal-income">+₱{{ number_format($dayData['income'],0) }}</div>
                    @endif
                    @if($dayData['expense'] > 0)
                        <div class="cal-amt cal-expense">-₱{{ number_format($dayData['expense'],0) }}</div>
                    @endif
                @endif
            </div>
        @endfor
    </div>
</div>

{{-- Day detail panel (shown on click) --}}
<div id="dayDetailPanel" class="app-card p-0 overflow-hidden d-none">
    <div class="cal-detail-header">
        <span id="dayDetailTitle" class="fw-bold" style="font-size:.9rem;"></span>
        <button class="cal-nav-btn" onclick="hideDayDetail()"><i class="ti ti-x"></i></button>
    </div>
    <div id="dayDetailBody" class="p-3" style="font-size:.85rem;"></div>
</div>

{{-- Hidden JSON for JS (pre-serialised in controller) --}}
<script id="dayMapData" type="application/json">@json($dayMap)</script>

@else
{{-- ── Table ─────────────────────────────────────────────────────────── --}}
<div class="app-card overflow-hidden">
    <div class="table-responsive">
        <table class="table app-table mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th class="text-end">Amount</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $tx)
                    <tr>
                        <td style="white-space:nowrap;color:#64748B;font-size:.8rem;">
                            {{ $tx->date->format('M d, Y') }}
                        </td>
                        <td>
                            <div style="font-weight:500;">{{ $tx->description }}</div>
                            @if ($tx->notes)
                                <div style="font-size:.75rem;color:#94A3B8;">{{ Str::limit($tx->notes, 50) }}</div>
                            @endif
                        </td>
                        <td>
                            @if ($tx->category)
                                <span class="cat-pill"
                                      style="background:{{ $tx->category->color ? $tx->category->color.'20' : '#F1F5F9' }};
                                             color:{{ $tx->category->color ?? '#475569' }};">
                                    {{ $tx->category->name }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="type-badge {{ $tx->type === 'Income' ? 'type-income' : 'type-expense' }}">
                                {{ $tx->type }}
                            </span>
                        </td>
                        <td class="text-end {{ $tx->type === 'Income' ? 'amount-income' : 'amount-expense' }}"
                            style="font-weight:600;white-space:nowrap;">
                            {{ $tx->type === 'Income' ? '+' : '-' }}₱{{ number_format($tx->amount, 2) }}
                        </td>
                        <td class="text-center" style="white-space:nowrap;">
                            {{-- Edit --}}
                            <button class="btn-action btn-action-edit"
                                    title="Edit"
                                    onclick="openEditModal(
                                        {{ $tx->id }},
                                        '{{ addslashes($tx->description) }}',
                                        {{ $tx->category_id }},
                                        '{{ $tx->type }}',
                                        '{{ number_format($tx->amount, 2, '.', '') }}',
                                        '{{ $tx->date->format('Y-m-d') }}',
                                        '{{ addslashes($tx->notes ?? '') }}'
                                    )">
                                <i class="ti ti-pencil"></i>
                            </button>
                            {{-- Delete --}}
                            <button class="btn-action btn-action-delete"
                                    title="Delete"
                                    onclick="openDeleteModal({{ $tx->id }})">
                                <i class="ti ti-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="ti ti-receipt-off" style="font-size:2.5rem;color:#CBD5E1;"></i>
                                </div>
                                <div class="empty-title">No transactions found</div>
                                <div class="empty-sub">
                                    @if (request()->hasAny(['type','category','month','search']))
                                        Try adjusting your filters or
                                        <a href="{{ route('transactions.index') }}">clear all</a>.
                                    @elseif ($categories->isEmpty())
                                        You need a category first.
                                        <a href="{{ route('budgets.index') }}">Create one in Budget Categories</a>.
                                    @else
                                        Click <strong>+ Add Transaction</strong> to record your first one.
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — ADD TRANSACTION
════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-custom">

            <div class="modal-header-custom">
                <h5 class="modal-title-custom" id="addModalLabel">
                    <i class="ti ti-circle-plus me-2"></i>Add Transaction
                </h5>
                <button type="button" class="modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <form action="{{ route('transactions.store') }}" method="POST" id="addTransactionForm">
                @csrf
                <div class="modal-body-custom">
                    @include('transactions._form', ['formId' => 'add'])
                </div>
                <div class="modal-footer-custom">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="ti ti-device-floppy me-1"></i> Save
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — EDIT TRANSACTION
════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editTransactionModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-custom">

            <div class="modal-header-custom">
                <h5 class="modal-title-custom" id="editModalLabel">
                    <i class="ti ti-pencil me-2"></i>Edit Transaction
                </h5>
                <button type="button" class="modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <form id="editTransactionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body-custom">
                    @include('transactions._form', ['formId' => 'edit'])
                </div>
                <div class="modal-footer-custom">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="ti ti-device-floppy me-1"></i> Update
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — DELETE CONFIRMATION
════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="deleteTransactionModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content modal-custom">

            <div class="modal-header-custom" style="background:#FEF2F2;border-bottom-color:#FECACA;">
                <h5 class="modal-title-custom" id="deleteModalLabel" style="color:#991B1B;">
                    <i class="ti ti-alert-triangle me-2"></i>Delete Transaction
                </h5>
                <button type="button" class="modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <div class="modal-body-custom text-center py-3">
                <p class="mb-0" style="font-size:.875rem;color:#374151;">
                    Are you sure you want to delete this transaction?<br>
                    <span style="font-size:.8rem;color:#94A3B8;">This action cannot be undone.</span>
                </p>
            </div>

            <form id="deleteTransactionForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-footer-custom">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4">
                        <i class="ti ti-trash me-1"></i> Delete
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@endif {{-- /calendar vs list --}}

@endsection


@push('styles')
<style>
    /* ── View toggle ── */
    .view-toggle-group { display:flex; background:#F3F4F6; border-radius:8px; padding:3px; gap:2px; }
    .view-toggle-btn { display:flex;align-items:center;gap:.3rem;padding:.3rem .75rem;border-radius:6px;font-size:.8rem;font-weight:600;color:#6B7280;text-decoration:none;transition:all .15s; }
    .view-toggle-btn.active { background:#fff;color:#1F2937;box-shadow:0 1px 3px rgba(0,0,0,.1); }
    .view-toggle-btn:hover:not(.active) { color:#374151; }

    /* ── Calendar ── */
    .cal-nav { display:flex;align-items:center;justify-content:space-between;padding:.75rem 1.25rem;border-bottom:1px solid #E5E7EB; }
    .cal-nav-title { font-size:.95rem;font-weight:700;color:#1F2937; }
    .cal-nav-btn { background:none;border:1px solid #E5E7EB;border-radius:7px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;color:#6B7280;cursor:pointer;text-decoration:none;transition:background .15s; }
    .cal-nav-btn:hover { background:#F3F4F6;color:#1F2937; }
    .cal-summary { display:flex;gap:1.5rem;flex-wrap:wrap;padding:.6rem 1.25rem;background:#F8FAFC;border-bottom:1px solid #E5E7EB;font-size:.8rem; }
    .cal-grid { display:grid;grid-template-columns:repeat(7,1fr);gap:0; }
    .cal-dow { padding:.5rem .25rem;text-align:center;font-size:.68rem;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid #E5E7EB; }
    .cal-day {
        min-height:72px;padding:.4rem .5rem;border-right:1px solid #F1F5F9;border-bottom:1px solid #F1F5F9;
        cursor:pointer;transition:background .1s;
    }
    .cal-day:nth-child(7n) { border-right:none; }
    .cal-day:hover { background:#F8FAFC; }
    .cal-day.cal-day-has-data { background:#FAFFFE; }
    .cal-day.cal-day-has-data:hover { background:#F0FDF4; }
    .cal-day-empty { background:#FAFAFA;cursor:default; }
    .cal-day-today { background:#EEF3EB !important; }
    .cal-day-num { font-size:.78rem;font-weight:600;color:#6B7280;margin-bottom:.2rem; }
    .cal-today-num { background:#7B9669;color:#fff;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;font-size:.72rem; }
    .cal-day.cal-selected { background:#EEF3EB; box-shadow:inset 0 0 0 2px #7B9669; }
    .cal-amt { font-size:.65rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .cal-income  { color:#059669; }
    .cal-expense { color:#DC2626; }
    .cal-detail-header { display:flex;align-items:center;justify-content:space-between;padding:.7rem 1.25rem;background:#F8FAFC;border-bottom:1px solid #E5E7EB; }
    .cal-txn-row { display:flex;align-items:center;gap:.6rem;padding:.45rem 0;border-bottom:1px solid #F3F4F6; }
    .cal-txn-row:last-child { border-bottom:none; }
    .cal-txn-cat { font-size:.72rem;color:#9CA3AF; }
    .cal-txn-amt { margin-left:auto;font-weight:700;font-size:.85rem; }

    /* ── Filter labels ── */
    .filter-label {
        display: block;
        font-size: .72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #64748B;
        margin-bottom: .25rem;
    }

    /* ── Category pill ── */
    .cat-pill {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        padding: .2rem .6rem;
        border-radius: 20px;
        font-size: .75rem;
        font-weight: 600;
    }

    /* ── Action buttons ── */
    .btn-action {
        width: 30px; height: 30px;
        border: none;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .75rem;
        cursor: pointer;
        transition: background .15s, transform .1s;
    }

    .btn-action:active { transform: scale(.92); }

    .btn-action-edit  { background: #EEF3EB; color: #7B9669; }
    .btn-action-edit:hover  { background: #D8E6D0; }
    .btn-action-delete { background: #FEF2F2; color: #EF4444; margin-left: .25rem; }
    .btn-action-delete:hover { background: #FEE2E2; }

    /* ── Empty state ── */
    .empty-state { text-align: center; padding: 3rem 1rem; }
    .empty-icon  { margin-bottom: .75rem; }
    .empty-title { font-weight: 600; color: #374151; margin-bottom: .35rem; }
    .empty-sub   { font-size: .8rem; color: #94A3B8; }
    .empty-sub a { color: #7B9669; text-decoration: none; }

    /* ── Modal ── */
    .modal-custom {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,.15);
    }

    .modal-header-custom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.1rem 1.5rem;
        background: #F8FAFC;
        border-bottom: 1px solid #E2E8F0;
    }

    .modal-title-custom {
        font-size: .95rem;
        font-weight: 700;
        color: #0F172A;
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        color: #94A3B8;
        font-size: .95rem;
        cursor: pointer;
        padding: .2rem .4rem;
        border-radius: 6px;
        transition: background .15s, color .15s;
    }

    .modal-close:hover { background: #E2E8F0; color: #374151; }

    .modal-body-custom { padding: 1.5rem; }

    .modal-footer-custom {
        display: flex;
        justify-content: flex-end;
        gap: .5rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid #E2E8F0;
        background: #F8FAFC;
    }

    /* ── Form elements inside modals ── */
    .modal-body-custom .form-label {
        font-size: .8rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: .3rem;
    }

    .modal-body-custom .form-control,
    .modal-body-custom .form-select {
        border: 1.5px solid #E5E7EB;
        border-radius: 8px;
        font-size: .875rem;
        padding: .5rem .75rem;
        transition: border-color .15s, box-shadow .15s;
    }

    .modal-body-custom .form-control:focus,
    .modal-body-custom .form-select:focus {
        border-color: #7B9669;
        box-shadow: 0 0 0 3px rgba(123,150,105,.12);
    }

    /* Type radio buttons */
    .type-radio-group {
        display: flex;
        gap: .5rem;
    }

    .type-radio-label {
        flex: 1;
        text-align: center;
        padding: .45rem;
        border: 1.5px solid #E5E7EB;
        border-radius: 8px;
        font-size: .8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
        user-select: none;
    }

    .type-radio-label:has(input:checked) { border-color: transparent; }

    .type-radio-income:has(input:checked)  { background: #D1FAE5; color: #065F46; border-color: #6EE7B7; }
    .type-radio-expense:has(input:checked) { background: #FEE2E2; color: #991B1B; border-color: #FCA5A5; }

    .type-radio-label input { display: none; }
</style>
@endpush


@push('scripts')
<script>
    // ── Calendar ─────────────────────────────────────────────────────────
    const dayMapEl = document.getElementById('dayMapData');
    const DAY_MAP  = dayMapEl ? JSON.parse(dayMapEl.textContent) : {};

    function showDayDetail(dateKey) {
        document.querySelectorAll('.cal-day').forEach(d => d.classList.remove('cal-selected'));
        const cell = document.querySelector(`.cal-day[data-date="${dateKey}"]`);
        if (cell) cell.classList.add('cal-selected');

        const data  = DAY_MAP[dateKey];
        const panel = document.getElementById('dayDetailPanel');
        const title = document.getElementById('dayDetailTitle');
        const body  = document.getElementById('dayDetailBody');

        const d = new Date(dateKey + 'T00:00:00');
        title.textContent = d.toLocaleDateString('en-PH', { weekday:'long', year:'numeric', month:'long', day:'numeric' });

        if (!data || !data.items.length) {
            body.innerHTML = '<p class="text-muted mb-0" style="font-size:.82rem;">No transactions on this date.</p>';
        } else {
            let html = '';
            const net = data.income - data.expense;
            html += `<div class="d-flex gap-3 mb-3 flex-wrap">
                <span style="font-size:.78rem;color:#059669;"><i class="ti ti-trending-up me-1"></i>Income: <strong>₱${data.income.toLocaleString('en',{minimumFractionDigits:2})}</strong></span>
                <span style="font-size:.78rem;color:#DC2626;"><i class="ti ti-trending-down me-1"></i>Expense: <strong>₱${data.expense.toLocaleString('en',{minimumFractionDigits:2})}</strong></span>
                <span style="font-size:.78rem;color:${net>=0?'#059669':'#DC2626'};"><strong>Net: ${net>=0?'+':''}₱${Math.abs(net).toLocaleString('en',{minimumFractionDigits:2})}</strong></span>
            </div>`;
            data.items.forEach(t => {
                const color = t.type === 'Income' ? '#059669' : '#DC2626';
                const sign  = t.type === 'Income' ? '+' : '-';
                html += `<div class="cal-txn-row">
                    <div>
                        <div style="font-size:.82rem;font-weight:600;color:#1F2937;">${escHtml(t.desc)}</div>
                        <div class="cal-txn-cat">${escHtml(t.cat)}</div>
                    </div>
                    <div class="cal-txn-amt" style="color:${color};">${sign}₱${parseFloat(t.amount).toLocaleString('en',{minimumFractionDigits:2})}</div>
                </div>`;
            });
            body.innerHTML = html;
        }
        panel.classList.remove('d-none');
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function hideDayDetail() {
        document.getElementById('dayDetailPanel').classList.add('d-none');
        document.querySelectorAll('.cal-day').forEach(d => d.classList.remove('cal-selected'));
    }

    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    // ── Edit modal ───────────────────────────────────────────────────────
    function openEditModal(id, description, categoryId, type, amount, date, notes) {
        const form = document.getElementById('editTransactionForm');
        form.action = `/transactions/${id}`;

        form.querySelector('#edit-description').value  = description;
        form.querySelector('#edit-category_id').value  = categoryId;
        form.querySelector('#edit-amount').value        = amount;
        form.querySelector('#edit-date').value          = date;
        form.querySelector('#edit-notes').value         = notes;

        // Set type radio
        form.querySelectorAll('input[name="type"]').forEach(r => {
            r.checked = (r.value === type);
        });

        new bootstrap.Modal(document.getElementById('editTransactionModal')).show();
    }

    // ── Delete modal ─────────────────────────────────────────────────────
    function openDeleteModal(id) {
        const form = document.getElementById('deleteTransactionForm');
        form.action = `/transactions/${id}`;
        new bootstrap.Modal(document.getElementById('deleteTransactionModal')).show();
    }

    // ── Re-open add modal on validation error ────────────────────────────
    @if ($errors->any() && old('_modal') === 'add')
        document.addEventListener('DOMContentLoaded', () => {
            new bootstrap.Modal(document.getElementById('addTransactionModal')).show();
        });
    @endif

    // ── Tooltips ─────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    });
</script>
@endpush
