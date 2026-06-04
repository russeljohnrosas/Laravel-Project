@extends('layouts.app')

@section('title', 'Budget Categories')

@php
/* Map category names to Tabler icons + accent colours.
   Matched case-insensitively; falls back to tag/gray. */
$iconMap = [
    'food'          => ['icon' => 'ti-tools-kitchen-2', 'bg' => '#10B981'],
    'dining'        => ['icon' => 'ti-tools-kitchen-2', 'bg' => '#10B981'],
    'restaurant'    => ['icon' => 'ti-tools-kitchen-2', 'bg' => '#10B981'],
    'groceries'     => ['icon' => 'ti-tools-kitchen-2', 'bg' => '#10B981'],
    'transport'     => ['icon' => 'ti-car',             'bg' => '#3B82F6'],
    'travel'        => ['icon' => 'ti-car',             'bg' => '#3B82F6'],
    'vehicle'       => ['icon' => 'ti-car',             'bg' => '#3B82F6'],
    'fuel'          => ['icon' => 'ti-car',             'bg' => '#3B82F6'],
    'entertainment' => ['icon' => 'ti-device-tv',       'bg' => '#EC4899'],
    'leisure'       => ['icon' => 'ti-device-tv',       'bg' => '#EC4899'],
    'movies'        => ['icon' => 'ti-device-tv',       'bg' => '#EC4899'],
    'shopping'      => ['icon' => 'ti-shopping-bag',    'bg' => '#F59E0B'],
    'clothing'      => ['icon' => 'ti-shopping-bag',    'bg' => '#F59E0B'],
    'utilities'     => ['icon' => 'ti-bolt',            'bg' => '#8B5CF6'],
    'bills'         => ['icon' => 'ti-bolt',            'bg' => '#8B5CF6'],
    'electric'      => ['icon' => 'ti-bolt',            'bg' => '#8B5CF6'],
    'water'         => ['icon' => 'ti-bolt',            'bg' => '#8B5CF6'],
    'health'        => ['icon' => 'ti-heart-rate-monitor', 'bg' => '#EF4444'],
    'medical'       => ['icon' => 'ti-heart-rate-monitor', 'bg' => '#EF4444'],
    'pharmacy'      => ['icon' => 'ti-heart-rate-monitor', 'bg' => '#EF4444'],
    'education'     => ['icon' => 'ti-school',          'bg' => '#06B6D4'],
    'school'        => ['icon' => 'ti-school',          'bg' => '#06B6D4'],
    'tuition'       => ['icon' => 'ti-school',          'bg' => '#06B6D4'],
    'savings'       => ['icon' => 'ti-piggy-bank',      'bg' => '#7B9669'],
    'investment'    => ['icon' => 'ti-chart-line',      'bg' => '#7B9669'],
    'rent'          => ['icon' => 'ti-home',            'bg' => '#64748B'],
    'housing'       => ['icon' => 'ti-home',            'bg' => '#64748B'],
    'subscription'  => ['icon' => 'ti-refresh',         'bg' => '#0EA5E9'],
    'insurance'     => ['icon' => 'ti-shield-check',    'bg' => '#6B7280'],
    'personal'      => ['icon' => 'ti-user',            'bg' => '#A78BFA'],
];

if (! function_exists('getCatMeta')) {
    function getCatMeta(string $name, array $map): array {
        $lower = strtolower($name);
        foreach ($map as $keyword => $meta) {
            if (str_contains($lower, $keyword)) {
                return $meta;
            }
        }
        return ['icon' => 'ti-tag', 'bg' => '#9CA3AF'];
    }
}
@endphp

@section('content')

{{-- ── Page header ───────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-0" style="font-size:1.25rem;color:#0F172A;">Budget Categories</h2>
        <p class="text-muted mb-0" style="font-size:.8rem;">
            {{ $month->format('F Y') }} &mdash; {{ $budgets->count() }} {{ Str::plural('budget', $budgets->count()) }}
        </p>
    </div>
    <form action="{{ route('budgets.index') }}" method="GET"
          class="d-flex align-items-center gap-2 flex-wrap">
        <select name="month" class="form-select form-select-sm" onchange="this.form.submit()"
                style="width:155px;font-size:.8rem;">
            @foreach ($monthOptions as $value => $label)
                <option value="{{ $value }}" {{ $month->format('Y-m') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <button type="button"
                class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                data-bs-toggle="modal" data-bs-target="#manageCategoriesModal">
            <i class="ti ti-plus"></i> Add Category
        </button>
    </form>
</div>

{{-- ── Tab navigation ─────────────────────────────────────────────────── --}}
<div class="budget-tab-nav mb-4">
    <button class="budget-tab-btn active" id="tab-btn-expense" onclick="switchBudgetTab('expense', this)">
        <i class="ti ti-trending-down"></i>
        Expenses
        <span class="budget-tab-count">{{ $expenseBudgets->count() }}</span>
    </button>
    <button class="budget-tab-btn" id="tab-btn-income" onclick="switchBudgetTab('income', this)">
        <i class="ti ti-trending-up"></i>
        Income
        <span class="budget-tab-count">{{ $incomeCategories->count() }}</span>
    </button>
</div>

{{-- ════════════════════════════════════════════════════════════════════
     EXPENSE TAB
════════════════════════════════════════════════════════════════════ --}}
<div id="tab-panel-expense" class="budget-tab-panel">

    {{-- Summary --}}
    @if ($expenseBudgets->isNotEmpty())
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4">
            <div class="summary-chip" style="border-left:4px solid #7B9669;">
                <div class="summary-label">Total Budgeted</div>
                <div class="summary-value" style="color:#7B9669;">₱{{ number_format($expenseTotalBudgeted, 2) }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="summary-chip" style="border-left:4px solid #EF4444;">
                <div class="summary-label">Total Spent</div>
                <div class="summary-value" style="color:#EF4444;">₱{{ number_format($expenseTotalSpent, 2) }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="summary-chip" style="border-left:4px solid #10B981;">
                <div class="summary-label">Total Remaining</div>
                <div class="summary-value" style="color:#10B981;">₱{{ number_format($expenseTotalRemaining, 2) }}</div>
            </div>
        </div>
    </div>
    @endif

    {{-- Cards --}}
    @if ($expenseBudgets->isNotEmpty())
        <div class="row g-3">
            @foreach ($expenseBudgets as $budget)
                @php
                    $pct = $budget->percentage;
                    if ($pct >= 100)      { $barColor = '#DC2626'; $statusText = 'Over budget'; $statusClass = 'status-over'; }
                    elseif ($pct >= 80)   { $barColor = '#D97706'; $statusText = 'Almost full';  $statusClass = 'status-warn'; }
                    else                  { $barColor = '#10B981'; $statusText = 'On track';      $statusClass = 'status-ok';   }
                    $catName = $budget->category?->name ?? 'Unknown';
                    $meta    = getCatMeta($catName, $iconMap);
                @endphp
                <div class="col-12 col-sm-6 col-xl-4" id="bcard-{{ $budget->id }}">
                    <div class="bcard">
                        <div class="bcard-header">
                            <div class="bcard-icon" style="background:{{ $meta['bg'] }};"><i class="ti {{ $meta['icon'] }}"></i></div>
                            <div class="bcard-meta">
                                <div class="bcard-name">{{ $catName }}</div>
                                <span class="bcard-status {{ $statusClass }}">{{ $statusText }}</span>
                            </div>
                            <div class="bcard-actions">
                                <button class="bcard-btn bcard-btn-edit" title="Edit"
                                        onclick="openEditBudgetModal({{ $budget->id }},{{ $budget->category_id }},'{{ number_format((float)$budget->amount,2,'.','')}}')"
                                ><i class="ti ti-pencil"></i></button>
                                <button class="bcard-btn bcard-btn-delete" title="Delete"
                                        onclick="openDeleteBudgetModal({{ $budget->id }})"
                                ><i class="ti ti-trash"></i></button>
                            </div>
                        </div>
                        <div class="bcard-amounts">
                            <div class="bamt"><span class="bamt-label">Budgeted</span><span class="bamt-value">₱{{ number_format($budget->amount,2) }}</span></div>
                            <div class="bamt-divider"></div>
                            <div class="bamt"><span class="bamt-label">Spent</span><span class="bamt-value" style="color:#DC2626;">₱{{ number_format($budget->spent,2) }}</span></div>
                            <div class="bamt-divider"></div>
                            <div class="bamt">
                                <span class="bamt-label">Remaining</span>
                                <span class="bamt-value" style="color:{{ $budget->remaining>=0?'#10B981':'#DC2626' }};">
                                    ₱{{ number_format(abs($budget->remaining),2) }}
                                    @if($budget->remaining<0)<span class="bamt-over">over</span>@endif
                                </span>
                            </div>
                        </div>
                        <div class="bcard-progress">
                            <div class="bprog-track"><div class="bprog-fill" style="width:{{ min($pct,100) }}%;background:{{ $barColor }};"></div></div>
                            <div class="bprog-meta">
                                <span>{{ $pct }}% of budget used</span>
                                @if($pct>=100)<span style="font-weight:700;color:#DC2626;">{{ number_format($pct-100,1) }}% over</span>@endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    {{-- Expense empty states --}}
    @else
        @if ($managedCategories->where('type','Expense')->isEmpty())
            <div class="app-card p-5 text-center">
                <div class="empty-step-icon" style="background:#EEF3EB;color:#7B9669;"><i class="ti ti-tag"></i></div>
                <h5 class="empty-title mt-3">Set up your first expense category</h5>
                <p class="empty-sub">Create an Expense category to start tracking your spending.</p>
                <button class="btn btn-primary btn-sm px-4 mt-3"
                        data-bs-toggle="modal" data-bs-target="#manageCategoriesModal">
                    <i class="ti ti-tag me-1"></i> Create a Category
                </button>
            </div>
        @elseif ($availableExpenseCategories->isEmpty())
            <div class="app-card p-5 text-center">
                <div class="empty-step-icon" style="background:#D1FAE5;color:#059669;"><i class="ti ti-circle-check"></i></div>
                <h5 class="empty-title mt-3">All expense categories budgeted!</h5>
                <p class="empty-sub">Every expense category has a budget for <strong>{{ $month->format('F Y') }}</strong>.</p>
                <button class="btn btn-outline-secondary btn-sm px-4 mt-3"
                        data-bs-toggle="modal" data-bs-target="#manageCategoriesModal">
                    <i class="ti ti-tag me-1"></i> Add More Categories
                </button>
            </div>
        @else
            <div class="app-card p-5 text-center">
                <div class="empty-step-icon" style="background:#EEF3EB;color:#7B9669;"><i class="ti ti-target"></i></div>
                <h5 class="empty-title mt-3">No expense budgets for {{ $month->format('F Y') }}</h5>
                <p class="empty-sub">Set spending limits per category to stay on track.</p>
                <button class="btn btn-primary btn-sm px-4 mt-1"
                        onclick="openAddBudgetModal('expense')">
                    <i class="ti ti-plus me-1"></i> Add Expense Budget
                </button>
            </div>
        @endif
    @endif

</div>{{-- /tab-panel-expense --}}


{{-- ════════════════════════════════════════════════════════════════════
     INCOME TAB
════════════════════════════════════════════════════════════════════ --}}
<div id="tab-panel-income" class="budget-tab-panel d-none">

    {{-- Summary strip --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4">
            <div class="summary-chip" style="border-left:4px solid #10B981;">
                <div class="summary-label">Total Earned This Month</div>
                <div class="summary-value" style="color:#10B981;">₱{{ number_format($incomeTotalEarned, 2) }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="summary-chip" style="border-left:4px solid #0EA5E9;">
                <div class="summary-label">Income Sources</div>
                <div class="summary-value" style="color:#0EA5E9;">{{ $incomeCategories->count() }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="summary-chip" style="border-left:4px solid #7B9669;">
                <div class="summary-label">Active Transactions</div>
                <div class="summary-value" style="color:#7B9669;">{{ $incomeCategories->sum('tx_count') }}</div>
            </div>
        </div>
    </div>

    @if ($incomeCategories->isEmpty())
        {{-- Empty state --}}
        <div class="app-card p-5 text-center">
            <div class="empty-step-icon" style="background:#E0F2FE;color:#0EA5E9;">
                <i class="ti ti-cash"></i>
            </div>
            <h5 class="empty-title mt-3">No income sources yet</h5>
            <p class="empty-sub">Add an income category and link it to an account to start tracking your earnings.</p>
            <button class="btn btn-sm px-4 mt-3" style="background:#0EA5E9;color:#fff;border-radius:8px;"
                    data-bs-toggle="modal" data-bs-target="#manageCategoriesModal">
                <i class="ti ti-plus me-1"></i> Add Income Source
            </button>
        </div>
    @else
        {{-- Income source cards --}}
        <div class="row g-3">
            @foreach ($incomeCategories as $cat)
                @php $meta = getCatMeta($cat->name, $iconMap); @endphp
                <div class="col-12 col-sm-6 col-xl-4" id="icard-{{ $cat->id }}">
                    <div class="income-card">

                        {{-- Header --}}
                        <div class="income-card-header">
                            <div class="income-card-icon" style="background:#E0F2FE;color:#0EA5E9;">
                                <i class="ti {{ $meta['icon'] }}"></i>
                            </div>
                            <div class="income-card-meta">
                                <div class="income-card-name">{{ $cat->name }}</div>
                                <span class="income-card-type">Income Source</span>
                            </div>
                            <div class="bcard-actions">
                                <button class="bcard-btn bcard-btn-edit" title="Edit"
                                        onclick="openEditIncomeCatModal(
                                            {{ $cat->id }},
                                            '{{ addslashes($cat->name) }}',
                                            '{{ $cat->account_id ?? '' }}'
                                        )">
                                    <i class="ti ti-pencil"></i>
                                </button>
                                <form action="{{ route('categories.destroy', $cat->id) }}" method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete income source \'{{ addslashes($cat->name) }}\'? Existing transactions will not be deleted.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="bcard-btn bcard-btn-delete" title="Delete">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Account badge --}}
                        @if ($cat->account)
                            <div class="income-account-badge">
                                <i class="ti ti-arrow-right me-1"></i>
                                Deposited to
                                <strong>{{ $cat->account->name }}</strong>
                                <span class="income-account-type">{{ $cat->account->type }}</span>
                            </div>
                        @else
                            <div class="income-account-badge income-account-unlinked">
                                <i class="ti ti-unlink me-1"></i>
                                No account linked
                                <button class="income-link-btn"
                                        onclick="openEditIncomeCatModal({{ $cat->id }},'{{ addslashes($cat->name) }}','')">
                                    Link account
                                </button>
                            </div>
                        @endif

                        {{-- Earned amount --}}
                        <div class="income-earned">
                            <div class="income-earned-amount">₱{{ number_format($cat->earned, 2) }}</div>
                            <div class="income-earned-label">earned in {{ $month->format('F Y') }}</div>
                        </div>

                        {{-- Footer --}}
                        <div class="income-card-footer">
                            <span class="income-tx-count">
                                <i class="ti ti-receipt me-1"></i>
                                {{ $cat->tx_count }} {{ Str::plural('transaction', $cat->tx_count) }}
                            </span>
                            @if ($cat->earned > 0)
                                <span class="income-status-badge">
                                    <i class="ti ti-circle-check me-1"></i>Active
                                </span>
                            @else
                                <span class="income-status-badge income-status-idle">
                                    <i class="ti ti-clock me-1"></i>No activity
                                </span>
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>{{-- /tab-panel-income --}}


{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — ADD BUDGET
════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="addBudgetModal" tabindex="-1" aria-labelledby="addBudgetLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content modal-custom">

            <div class="modal-header-custom">
                <h5 class="modal-title-custom" id="addBudgetLabel">
                    <i class="ti ti-circle-plus me-2"></i>Add Budget
                    <span class="modal-month-tag ms-2">{{ $month->format('F Y') }}</span>
                </h5>
                <button type="button" class="modal-close" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <form action="{{ route('budgets.store') }}" method="POST">
                @csrf
                <input type="hidden" name="month" value="{{ $month->format('Y-m') }}">

                <div class="modal-body-custom">

                    {{-- Category preview bar (shown after selection) --}}
                    <div id="catPreview" class="cat-preview-bar d-none mb-3">
                        <div id="catPreviewIcon" class="cat-preview-icon">
                            <i id="catPreviewIconEl" class="ti ti-tag"></i>
                        </div>
                        <div>
                            <div id="catPreviewName" class="cat-preview-name">—</div>
                            <div id="catPreviewHint" class="cat-preview-hint">Category</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="add-category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select id="add-category_id" name="category_id"
                                class="form-select @error('category_id') is-invalid @enderror"
                                onchange="updateCatPreview(this)"
                                required>
                            <option value="">— Select a category —</option>
                            @if($availableExpenseCategories->isNotEmpty())
                                <optgroup label="Expense">
                                    @foreach ($availableExpenseCategories as $cat)
                                        <option value="{{ $cat->id }}" data-name="{{ $cat->name }}" data-type="Expense"
                                                {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if($availableIncomeCategories->isNotEmpty())
                                <optgroup label="Income">
                                    @foreach ($availableIncomeCategories as $cat)
                                        <option value="{{ $cat->id }}" data-name="{{ $cat->name }}" data-type="Income"
                                                {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="add-amount" id="add-amount-label" class="form-label">Monthly Budget Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text modal-input-addon">₱</span>
                            <input type="number" id="add-amount" name="amount"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   placeholder="0.00" step="0.01" min="0.01"
                                   value="{{ old('amount') }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div id="add-amount-hint" class="form-text" style="font-size:.75rem;color:#94A3B8;">
                            Set the maximum you plan to spend this month.
                        </div>
                    </div>
                </div>

                <div class="modal-footer-custom">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="ti ti-circle-plus me-1"></i> Add Budget
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — EDIT BUDGET
════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editBudgetModal" tabindex="-1" aria-labelledby="editBudgetLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content modal-custom">

            <div class="modal-header-custom">
                <h5 class="modal-title-custom" id="editBudgetLabel">
                    <i class="ti ti-pencil me-2"></i>Edit Budget
                </h5>
                <button type="button" class="modal-close" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <form id="editBudgetForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body-custom">
                    <div class="mb-3">
                        <label for="edit-category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select id="edit-category_id" name="category_id" class="form-select" required>
                            <option value="">Select a category</option>
                            @foreach ($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="edit-amount" class="form-label">Budget Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text modal-input-addon">₱</span>
                            <input type="number" id="edit-amount" name="amount"
                                   class="form-control" placeholder="0.00"
                                   step="0.01" min="0.01" required>
                        </div>
                    </div>
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
<div class="modal fade" id="deleteBudgetModal" tabindex="-1" aria-labelledby="deleteBudgetLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content modal-custom">

            <div class="modal-header-custom" style="background:#FEF2F2;border-bottom-color:#FECACA;">
                <h5 class="modal-title-custom" id="deleteBudgetLabel" style="color:#991B1B;">
                    <i class="ti ti-alert-triangle me-2"></i>Delete Budget
                </h5>
                <button type="button" class="modal-close" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <div class="modal-body-custom text-center py-3">
                <p class="mb-0" style="font-size:.875rem;color:#374151;">
                    Are you sure you want to delete this budget?<br>
                    <span style="font-size:.8rem;color:#94A3B8;">Transactions will not be affected.</span>
                </p>
            </div>

            <form id="deleteBudgetForm" method="POST">
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

{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — MANAGE CATEGORIES
════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="manageCategoriesModal" tabindex="-1" aria-labelledby="manageCategoriesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:900px;">
        <div class="modal-content modal-custom" style="min-height:870px;">

            <div class="modal-header-custom">
                <h5 class="modal-title-custom" id="manageCategoriesLabel">
                    <i class="ti ti-tag me-2"></i>Manage Categories
                </h5>
                <button type="button" class="modal-close" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            {{-- Add new category form ── --}}
            <div class="cat-add-bar">
                <form id="addCatForm" action="{{ route('categories.store') }}" method="POST" class="d-flex gap-2 align-items-end flex-wrap">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month->format('Y-m') }}">

                    <div class="flex-grow-1" style="min-width:110px;">
                        <label class="form-label mb-1">Category Name</label>
                        <input type="text" name="name"
                               class="form-control form-control-sm @error('name') is-invalid @enderror"
                               placeholder="e.g. Groceries"
                               value="{{ old('name') }}"
                               maxlength="100" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div style="width:105px;">
                        <label class="form-label mb-1">Type</label>
                        <select name="type" class="form-select form-select-sm @error('type') is-invalid @enderror"
                                onchange="toggleCatTypeFields(this)" required>
                            <option value="Expense" {{ old('type', 'Expense') === 'Expense' ? 'selected' : '' }}>Expense</option>
                            <option value="Income"  {{ old('type') === 'Income'              ? 'selected' : '' }}>Income</option>
                        </select>
                    </div>

                    {{-- Expense: budget amount + Add button --}}
                    <div id="budgetAmtWrap">
                        <label id="budgetAmtLabel" class="form-label mb-1">Budget (₱)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" id="budgetAmtInput" name="budget_amount"
                                   class="form-control"
                                   placeholder="0.00" step="0.01" min="0.01"
                                   style="width:90px;" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i>Add
                            </button>
                        </div>
                    </div>

                    {{-- Income: account picker + Add button --}}
                    <div id="incomeAccountWrap" style="display:none;">
                        <label class="form-label mb-1">Deposit to</label>
                        <div class="input-group input-group-sm">
                            <select name="account_id" id="incomeAccountSelect"
                                    class="form-select" style="width:110px;">
                                <option value="">— Account —</option>
                                @foreach ($userAccounts as $acct)
                                    <option value="{{ $acct->id }}">{{ $acct->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i>Add
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Category list ── --}}
            <div class="modal-body-custom p-0">
                @if ($managedCategories->isEmpty())
                    <div class="cat-empty">
                        <i class="ti ti-tag" style="font-size:2rem;color:#BAC8B1;"></i>
                        <p class="mt-2 mb-0" style="font-size:.825rem;color:#94A3B8;">No categories yet. Add one above.</p>
                    </div>
                @else
                    <ul class="cat-list">
                        @foreach ($managedCategories as $cat)
                            <li class="cat-item" id="cat-row-{{ $cat->id }}">

                                {{-- View mode --}}
                                <div class="cat-view d-flex align-items-center gap-2">
                                    <span class="cat-type-badge cat-type-{{ strtolower($cat->type) }}">
                                        {{ $cat->type }}
                                    </span>
                                    <div class="flex-grow-1 min-w-0">
                                        <span class="cat-name">{{ $cat->name }}</span>
                                        @if ($cat->type === 'Income' && $cat->account)
                                            <span class="cat-account-hint">
                                                <i class="ti ti-arrow-right"></i> {{ $cat->account->name }}
                                            </span>
                                        @endif
                                    </div>
                                    <button type="button"
                                            class="bcard-btn bcard-btn-edit"
                                            onclick="openCatEdit({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ $cat->type }}', '{{ $cat->account_id ?? '' }}')"
                                            title="Edit">
                                        <i class="ti ti-pencil"></i>
                                    </button>
                                    <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete category \'{{ addslashes($cat->name) }}\'? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bcard-btn bcard-btn-delete" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                {{-- Edit mode (hidden by default) --}}
                                <form class="cat-edit-form d-none d-flex gap-2 align-items-end flex-wrap"
                                      action="{{ route('categories.update', $cat->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="flex-grow-1" style="min-width:100px;">
                                        <input type="text" name="name"
                                               class="form-control form-control-sm"
                                               value="{{ $cat->name }}"
                                               maxlength="100" required>
                                    </div>
                                    <div style="width:100px;">
                                        <select name="type" class="form-select form-select-sm cat-edit-type-sel">
                                            <option value="Expense" {{ $cat->type === 'Expense' ? 'selected' : '' }}>Expense</option>
                                            <option value="Income"  {{ $cat->type === 'Income'  ? 'selected' : '' }}>Income</option>
                                        </select>
                                    </div>
                                    <div class="cat-edit-account-wrap {{ $cat->type === 'Income' ? '' : 'd-none' }}" style="width:120px;">
                                        <select name="account_id" class="form-select form-select-sm">
                                            <option value="">— Account —</option>
                                            @foreach ($userAccounts as $acct)
                                                <option value="{{ $acct->id }}"
                                                    {{ $cat->account_id == $acct->id ? 'selected' : '' }}>
                                                    {{ $acct->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm px-2 flex-shrink-0" style="height:32px;" title="Save">
                                        <i class="ti ti-device-floppy"></i>
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm px-2 flex-shrink-0" style="height:32px;"
                                            onclick="closeCatEdit({{ $cat->id }})" title="Cancel">
                                        <i class="ti ti-x"></i>
                                    </button>
                                </form>

                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="modal-footer-custom" style="padding:1.25rem 50px;">
                <small id="catCountBadge" class="text-muted me-auto" style="font-size:.8rem;">
                    {{ $managedCategories->count() }} {{ Str::plural('category', $managedCategories->count()) }}
                </small>
                <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — EDIT INCOME CATEGORY
════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editIncomeCatModal" tabindex="-1" aria-labelledby="editIncomeCatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content modal-custom">

            <div class="modal-header-custom">
                <h5 class="modal-title-custom" id="editIncomeCatLabel">
                    <i class="ti ti-pencil me-2"></i>Edit Income Source
                </h5>
                <button type="button" class="modal-close" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <form id="editIncomeCatForm" method="POST">
                @csrf @method('PUT')

                <div class="modal-body-custom">

                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" id="editIncomeCatName" name="name"
                               class="form-control" maxlength="100" required>
                    </div>

                    <input type="hidden" name="type" value="Income">

                    <div>
                        <label class="form-label">Deposit to Account</label>
                        <select id="editIncomeCatAccount" name="account_id" class="form-select">
                            <option value="">— No account linked —</option>
                            @foreach ($userAccounts as $acct)
                                <option value="{{ $acct->id }}">
                                    {{ $acct->name }}
                                    <span style="color:#9CA3AF;">({{ $acct->type }})</span>
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text" style="font-size:.75rem;color:#94A3B8;">
                            Select which account this income is deposited into.
                        </div>
                    </div>

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


@endsection


@push('styles')
<style>
    /* ── Budget tabs ── */
    .budget-tab-nav {
        display: flex;
        gap: 0;
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 10px;
        padding: 4px;
    }

    .budget-tab-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        padding: .55rem 1rem;
        border: none;
        border-radius: 7px;
        background: transparent;
        color: #6B7280;
        font-size: .85rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s, color .15s;
    }

    .budget-tab-btn:hover:not(.active) { background: #F3F4F6; color: #374151; }

    .budget-tab-btn.active {
        background: #7B9669;
        color: #fff;
        box-shadow: 0 1px 4px rgba(123,150,105,.35);
    }

    #tab-btn-income.active { background: #0EA5E9; box-shadow: 0 1px 4px rgba(14,165,233,.35); }

    .budget-tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        padding: 0 5px;
        border-radius: 10px;
        font-size: .7rem;
        font-weight: 700;
        background: rgba(255,255,255,.25);
    }

    .budget-tab-btn:not(.active) .budget-tab-count { background: #E5E7EB; color: #6B7280; }

    .budget-tab-panel { animation: tabFade .2s ease; }
    @keyframes tabFade { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }

    /* Income card accent */
    /* ── Income source cards ── */
    .income-card {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-top: 3px solid #0EA5E9;
        border-radius: 10px;
        padding: 1.1rem 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        display: flex;
        flex-direction: column;
        gap: .75rem;
        height: 100%;
        transition: box-shadow .2s, transform .2s;
    }
    .income-card:hover { box-shadow: 0 4px 14px rgba(14,165,233,.12); transform: translateY(-2px); }

    .income-card-header { display: flex; align-items: flex-start; gap: .7rem; }
    .income-card-icon {
        width: 38px; height: 38px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; flex-shrink: 0;
    }
    .income-card-meta { flex: 1; min-width: 0; }
    .income-card-name { font-size: .9rem; font-weight: 600; color: #1F2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .income-card-type { font-size: .68rem; font-weight: 600; color: #0EA5E9; text-transform: uppercase; letter-spacing: .04em; }

    .income-account-badge {
        display: flex;
        align-items: center;
        gap: .3rem;
        font-size: .78rem;
        color: #374151;
        background: #F0F9FF;
        border: 1px solid #BAE6FD;
        border-radius: 6px;
        padding: .35rem .65rem;
    }
    .income-account-badge strong { color: #0369A1; }
    .income-account-type {
        margin-left: .25rem;
        font-size: .65rem;
        font-weight: 600;
        background: #E0F2FE;
        color: #0369A1;
        padding: .05rem .35rem;
        border-radius: 4px;
    }
    .income-account-unlinked { background: #F9FAFB; border-color: #E5E7EB; color: #9CA3AF; }
    .income-link-btn {
        margin-left: auto;
        background: none;
        border: none;
        color: #0EA5E9;
        font-size: .72rem;
        font-weight: 600;
        cursor: pointer;
        padding: 0;
        text-decoration: underline;
    }

    .income-earned { text-align: center; padding: .5rem 0; }
    .income-earned-amount { font-size: 1.5rem; font-weight: 800; color: #10B981; letter-spacing: -.02em; }
    .income-earned-label { font-size: .72rem; color: #9CA3AF; margin-top: .1rem; }

    .income-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: .5rem;
        border-top: 1px solid #F1F5F9;
        font-size: .75rem;
    }
    .income-tx-count { color: #6B7280; }
    .income-status-badge {
        display: flex;
        align-items: center;
        font-size: .68rem;
        font-weight: 600;
        background: #D1FAE5;
        color: #065F46;
        padding: .15rem .5rem;
        border-radius: 5px;
    }
    .income-status-idle { background: #F3F4F6; color: #9CA3AF; }

    .cat-account-hint { display: block; font-size: .68rem; color: #0EA5E9; margin-top: .1rem; }

    /* ── New card highlight animation ── */
    @keyframes cardPulse {
        0%   { box-shadow: 0 0 0 0 rgba(123,150,105,.5); border-color: #7B9669; }
        60%  { box-shadow: 0 0 0 8px rgba(123,150,105,0); border-color: #7B9669; }
        100% { box-shadow: none; border-color: #E5E7EB; }
    }
    .bcard-new { animation: cardPulse 1.6s ease forwards; }

    /* ── Add budget modal extras ── */
    .modal-month-tag {
        font-size: .7rem;
        font-weight: 600;
        background: rgba(123,150,105,.15);
        color: #7B9669;
        padding: .15rem .5rem;
        border-radius: 4px;
        vertical-align: middle;
    }

    .cat-preview-bar {
        display: flex;
        align-items: center;
        gap: .75rem;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        padding: .75rem 1rem;
    }

    .cat-preview-icon {
        width: 38px; height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1rem;
        flex-shrink: 0;
        transition: background .2s;
    }

    .cat-preview-name { font-size: .9rem; font-weight: 600; color: #0F172A; }
    .cat-preview-hint { font-size: .72rem; color: #94A3B8; }

    /* ── Summary chips ── */
    .summary-chip {
        background: #fff;
        border-radius: 8px;
        padding: .9rem 1.1rem;
        border: 1px solid #E5E7EB;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
    }
    .summary-label {
        font-size: .7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #9CA3AF;
        margin-bottom: .25rem;
    }
    .summary-value { font-size: 1.2rem; font-weight: 700; line-height: 1; }

    /* ═══════════════════════════════════════
       BUDGET CARD
    ══════════════════════════════════════ */
    .bcard {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,.08);
        display: flex;
        flex-direction: column;
        gap: 1rem;
        height: 100%;
        transition: box-shadow .2s, transform .2s;
    }
    .bcard:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,.1);
        transform: translateY(-2px);
    }

    /* Header */
    .bcard-header {
        display: flex;
        align-items: center;
        gap: .75rem;
    }
    .bcard-icon {
        width: 40px; height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #fff;
        font-size: 1.05rem;
    }
    .bcard-meta { flex: 1; min-width: 0; }
    .bcard-name {
        font-size: .9rem;
        font-weight: 600;
        color: #1F2937;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .bcard-status {
        display: inline-block;
        padding: .1rem .5rem;
        border-radius: 4px;
        font-size: .68rem;
        font-weight: 600;
        margin-top: .2rem;
    }
    .status-ok   { background: #D1FAE5; color: #065F46; }
    .status-warn { background: #FEF3C7; color: #92400E; }
    .status-over { background: #FEE2E2; color: #991B1B; }

    /* Action buttons */
    .bcard-actions { display: flex; gap: .25rem; flex-shrink: 0; }
    .bcard-btn {
        width: 28px; height: 28px;
        border: none;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .8rem;
        cursor: pointer;
        transition: background .15s, transform .1s;
    }
    .bcard-btn:active { transform: scale(.9); }
    .bcard-btn-edit   { background: #EEF3EB; color: #7B9669; }
    .bcard-btn-edit:hover   { background: #D8E6D0; }
    .bcard-btn-delete { background: #FEF2F2; color: #EF4444; }
    .bcard-btn-delete:hover { background: #FEE2E2; }

    /* Amounts */
    .bcard-amounts {
        display: flex;
        align-items: center;
        background: #F9FAFB;
        border-radius: 8px;
        border: 1px solid #F1F5F9;
        padding: .65rem .75rem;
    }
    .bamt {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .15rem;
    }
    .bamt-label {
        font-size: .62rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #9CA3AF;
    }
    .bamt-value { font-size: .875rem; font-weight: 700; color: #1F2937; line-height: 1; }
    .bamt-over  { font-size: .6rem; font-weight: 700; color: #DC2626; }
    .bamt-divider {
        width: 1px; height: 28px;
        background: #E5E7EB;
        flex-shrink: 0;
        margin: 0 .25rem;
    }

    /* Progress */
    .bcard-progress { margin-top: auto; }
    .bprog-track {
        height: 8px;
        background: #E5E7EB;
        border-radius: 4px;
        overflow: hidden;
    }
    .bprog-fill {
        height: 100%;
        border-radius: 4px;
        transition: width .4s ease;
    }
    .bprog-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: .35rem;
        font-size: .72rem;
        color: #9CA3AF;
    }

    /* ═══════════════════════════════════════
       MODALS
    ══════════════════════════════════════ */
    .modal-custom       { border:none; border-radius:12px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.15); }
    .modal-header-custom { display:flex; align-items:center; justify-content:space-between; padding:1rem 1.5rem; background:#F8FAFC; border-bottom:1px solid #E2E8F0; }
    .modal-title-custom  { font-size:.925rem; font-weight:700; color:#0F172A; margin:0; }
    .modal-close         { background:none; border:none; color:#94A3B8; font-size:.95rem; cursor:pointer; padding:.2rem .4rem; border-radius:6px; transition:background .15s,color .15s; }
    .modal-close:hover   { background:#E2E8F0; color:#374151; }
    .modal-body-custom   { padding:1.5rem; }
    .modal-footer-custom { display:flex; justify-content:flex-end; gap:.5rem; padding:1rem 1.5rem; border-top:1px solid #E2E8F0; background:#F8FAFC; }

    .modal-body-custom .form-label  { font-size:.8rem; font-weight:600; color:#374151; margin-bottom:.3rem; }
    .modal-body-custom .form-select,
    .modal-body-custom .form-control { border:1.5px solid #E5E7EB; border-radius:8px; font-size:.875rem; padding:.5rem .75rem; }
    .modal-body-custom .form-select:focus,
    .modal-body-custom .form-control:focus { border-color:#7B9669; box-shadow:0 0 0 3px rgba(123,150,105,.12); }

    .modal-input-addon {
        background:#F8FAFC; border:1.5px solid #E5E7EB; border-right:none;
        border-radius:8px 0 0 8px; font-size:.875rem; color:#64748B;
    }
    .modal-input-addon + .form-control {
        border:1.5px solid #E5E7EB; border-left:none;
        border-radius:0 8px 8px 0;
    }
    .modal-input-addon + .form-control:focus {
        border-color:#7B9669; box-shadow:0 0 0 3px rgba(123,150,105,.12);
    }

    /* ── All-budgeted notice ── */
    .alert-all-budgeted {
        background: #F0FDF4;
        border: 1px solid #BBF7D0;
        border-radius: 8px;
        padding: .65rem 1rem;
        font-size: .825rem;
        color: #166534;
    }

    .alert-all-budgeted a { color: #059669; font-weight: 600; text-decoration: none; }
    .alert-all-budgeted a:hover { text-decoration: underline; }

    /* ── Empty states ── */
    .empty-step-icon {
        width: 64px; height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin: 0 auto;
    }

    .empty-title { font-size: 1rem; font-weight: 700; color: #1F2937; }
    .empty-sub   { font-size: .825rem; color: #94A3B8; margin-bottom: 0; }

    .empty-steps {
        display: flex;
        flex-direction: column;
        gap: .5rem;
        max-width: 280px;
        margin: 1.25rem auto 0;
        text-align: left;
    }

    .empty-step {
        display: flex;
        align-items: center;
        gap: .75rem;
        font-size: .825rem;
        color: #94A3B8;
    }

    .empty-step.active { color: #374151; font-weight: 600; }

    .step-num {
        width: 22px; height: 22px;
        border-radius: 50%;
        background: #E5E7EB;
        color: #9CA3AF;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .7rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    .empty-step.active .step-num {
        background: #7B9669;
        color: #fff;
    }

    /* ── Manage Categories modal ── */
    .cat-add-bar {
        padding: 50px;
        background: #F8FAFC;
        border-bottom: 1px solid #E2E8F0;
    }
    .cat-add-bar .form-label { font-size: .8rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .cat-add-bar .gap-2 { gap: 20px !important; }
    .cat-add-bar .form-control,
    .cat-add-bar .form-select  { border: 1.5px solid #E5E7EB; border-radius: 7px; font-size: .9rem; }
    .cat-add-bar .form-control:focus,
    .cat-add-bar .form-select:focus { border-color: #7B9669; box-shadow: 0 0 0 3px rgba(123,150,105,.12); }

    .cat-empty { padding: 2.5rem 50px; text-align: center; }

    .cat-list { list-style: none; margin: 0; padding: 0; }

    .cat-item {
        padding: 15px 50px;
        border-bottom: 1px solid #F1F5F9;
        margin-bottom: 12px;
    }
    .cat-item:last-child { border-bottom: none; margin-bottom: 0; }

    .cat-edit-form .form-control,
    .cat-edit-form .form-select { border: 1.5px solid #E5E7EB; border-radius: 7px; font-size: .85rem; }
    .cat-edit-form .form-control:focus,
    .cat-edit-form .form-select:focus { border-color: #7B9669; box-shadow: 0 0 0 3px rgba(123,150,105,.12); }

    .cat-name { font-size: .875rem; font-weight: 500; color: #1F2937; }

    .cat-type-badge {
        display: inline-block;
        padding: .15rem .55rem;
        border-radius: 5px;
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .03em;
        flex-shrink: 0;
    }
    .cat-type-expense { background: #FEE2E2; color: #991B1B; }
    .cat-type-income  { background: #D1FAE5; color: #065F46; }
</style>
@endpush


@push('scripts')
<script>
    // ── Icon map (mirrors the PHP $iconMap) ──────────────────────────────
    const ICON_MAP = @json($iconMap);

    function resolveCatMeta(name) {
        const lower = name.toLowerCase();
        for (const [keyword, meta] of Object.entries(ICON_MAP)) {
            if (lower.includes(keyword)) return meta;
        }
        return { icon: 'ti-tag', bg: '#9CA3AF' };
    }

    // ── Tab switching ────────────────────────────────────────────────────
    function switchBudgetTab(tab, btn) {
        document.querySelectorAll('.budget-tab-panel').forEach(p => p.classList.add('d-none'));
        document.querySelectorAll('.budget-tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-panel-' + tab).classList.remove('d-none');
        btn.classList.add('active');
        sessionStorage.setItem('activeBudgetTab', tab);
    }

    // Restore active tab on page load
    document.addEventListener('DOMContentLoaded', () => {
        const saved = sessionStorage.getItem('activeBudgetTab');
        if (saved === 'income') {
            switchBudgetTab('income', document.getElementById('tab-btn-income'));
        }
    });

    // ── Open Add Budget modal pre-filtered by tab ────────────────────────
    function openAddBudgetModal(type) {
        const select = document.getElementById('add-category_id');
        // Pre-scroll the optgroup that matches the type into view after modal opens
        const modal = new bootstrap.Modal(document.getElementById('addBudgetModal'));
        modal.show();
        document.getElementById('addBudgetModal').addEventListener('shown.bs.modal', function handler() {
            // Focus first option of matching group if available
            const opts = Array.from(select.options);
            const match = opts.find(o => o.dataset.type === (type === 'income' ? 'Income' : 'Expense'));
            if (match) { select.value = match.value; updateCatPreview(select); }
            this.removeEventListener('shown.bs.modal', handler);
        });
    }

    // ── Live category preview in Add Budget modal ────────────────────────
    function updateCatPreview(select) {
        const preview = document.getElementById('catPreview');
        const opt     = select.options[select.selectedIndex];

        if (!opt.value) { preview.classList.add('d-none'); return; }

        const name    = opt.dataset.name;
        const catType = opt.dataset.type ?? 'Expense';
        const meta    = catType === 'Income'
            ? { icon: resolveCatMeta(name).icon, bg: '#0EA5E9' }
            : resolveCatMeta(name);

        document.getElementById('catPreviewIconEl').className  = `ti ${meta.icon}`;
        document.getElementById('catPreviewIcon').style.background = meta.bg;
        document.getElementById('catPreviewName').textContent  = name;
        document.getElementById('catPreviewHint').textContent  = catType + ' category';

        // Update amount label/hint based on type
        const amtLabel = document.getElementById('add-amount-label');
        const amtHint  = document.getElementById('add-amount-hint');
        if (catType === 'Income') {
            amtLabel.firstChild.textContent = 'Monthly Income Target ';
            amtHint.textContent = 'Set the income amount you aim to earn this month.';
        } else {
            amtLabel.firstChild.textContent = 'Monthly Budget Amount ';
            amtHint.textContent = 'Set the maximum you plan to spend this month.';
        }

        preview.classList.remove('d-none');
    }

    // Re-trigger preview if old value is pre-selected (validation error reopen)
    document.addEventListener('DOMContentLoaded', () => {
        const sel = document.getElementById('add-category_id');
        if (sel && sel.value) updateCatPreview(sel);
    });

    // ── Edit / delete modals ─────────────────────────────────────────────
    function openEditBudgetModal(id, categoryId, amount) {
        const form = document.getElementById('editBudgetForm');
        form.action = `/budgets/${id}`;
        form.querySelector('#edit-category_id').value = categoryId;
        form.querySelector('#edit-amount').value = amount;
        new bootstrap.Modal(document.getElementById('editBudgetModal')).show();
    }

    function openDeleteBudgetModal(id) {
        const form = document.getElementById('deleteBudgetForm');
        form.action = `/budgets/${id}`;
        new bootstrap.Modal(document.getElementById('deleteBudgetModal')).show();
    }

    // ── Auto-open modals on validation errors or flash ───────────────────
    @if ($errors->hasAny(['category_id', 'amount']))
        document.addEventListener('DOMContentLoaded', () => {
            new bootstrap.Modal(document.getElementById('addBudgetModal')).show();
        });
    @elseif ($errors->hasAny(['name', 'type']) || session('open_categories_modal'))
        document.addEventListener('DOMContentLoaded', () => {
            new bootstrap.Modal(document.getElementById('manageCategoriesModal')).show();
        });
    @endif

    // ── Highlight & scroll to newly created budget card ──────────────────
    @if (session('new_budget_id'))
        document.addEventListener('DOMContentLoaded', () => {
            const card = document.getElementById('bcard-{{ session('new_budget_id') }}');
            if (card) {
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                card.querySelector('.bcard').classList.add('bcard-new');
            }
        });
    @endif

    // ── Add Category – AJAX ──────────────────────────────────────────────
    document.getElementById('addCatForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const form      = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const original  = submitBtn.innerHTML;

        submitBtn.disabled  = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        try {
            const res = await fetch(form.action, {
                method:  'POST',
                headers: {
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: new FormData(form),
            });

            // Validation errors (422)
            if (res.status === 422) {
                const json   = await res.json();
                const errors = Object.values(json.errors ?? {}).flat();
                showToast(errors.join(' ') || 'Validation error.', 'error');
                return;
            }

            const data = await res.json();

            if (!data.success) {
                showToast(data.message || 'Failed to create category.', 'error');
                return;
            }

            showToast(data.message, 'success');

            // A budget card was created — reload so the card grid renders correctly
            if (data.budget_id) {
                sessionStorage.setItem('newBudgetId',   String(data.budget_id));
                sessionStorage.setItem('reopenCatModal', '1');
                // Switch to the correct tab after reload so the new card is visible
                sessionStorage.setItem('activeBudgetTab', data.category.type === 'Income' ? 'income' : 'expense');
                window.location.reload();
                return;
            }

            form.reset();
            toggleCatTypeFields(form.querySelector('select[name="type"]'));

            const hadList = !!document.querySelector('.cat-list');

            if (!hadList) {
                sessionStorage.setItem('reopenCatModal', '1');
                window.location.reload();
                return;
            }

            appendCatToModal(data.category);

            if (data.category.type === 'Expense') {
                addCatToDropdown(data.category);
                const banner = document.querySelector('.alert-all-budgeted');
                if (banner) banner.remove();
            }

        } catch (err) {
            showToast('An error occurred. Please try again.', 'error');
        } finally {
            submitBtn.disabled  = false;
            submitBtn.innerHTML = original;
        }
    });

    // ── Toggle fields in Add Category form based on type ─────────────────
    function toggleCatTypeFields(select) {
        const budgetWrap  = document.getElementById('budgetAmtWrap');
        const budgetInput = document.getElementById('budgetAmtInput');
        const accountWrap = document.getElementById('incomeAccountWrap');
        if (!budgetWrap) return;
        const isExpense = select.value === 'Expense';
        budgetWrap.style.display  = isExpense ? '' : 'none';
        budgetInput.required      = isExpense;
        if (!isExpense) budgetInput.value = '';
        accountWrap.style.display = isExpense ? 'none' : '';
    }

    // Initialise on page load
    document.addEventListener('DOMContentLoaded', () => {
        const typeSelect = document.querySelector('#addCatForm select[name="type"]');
        if (typeSelect) toggleCatTypeFields(typeSelect);
    });

    // ── Edit Income Category modal ────────────────────────────────────────
    function openEditIncomeCatModal(id, name, accountId) {
        const form = document.getElementById('editIncomeCatForm');
        form.action = '/categories/' + id;
        document.getElementById('editIncomeCatName').value = name;
        const acctSel = document.getElementById('editIncomeCatAccount');
        acctSel.value = accountId ?? '';
        new bootstrap.Modal(document.getElementById('editIncomeCatModal')).show();
    }

    // ── Re-open modal after reload ───────────────────────────────────────
    if (sessionStorage.getItem('reopenCatModal')) {
        sessionStorage.removeItem('reopenCatModal');
        document.addEventListener('DOMContentLoaded', () => {
            new bootstrap.Modal(document.getElementById('manageCategoriesModal')).show();
        });
    }

    // ── Highlight newly created budget card after reload ─────────────────
    const _newBid = sessionStorage.getItem('newBudgetId');
    if (_newBid) {
        sessionStorage.removeItem('newBudgetId');
        document.addEventListener('DOMContentLoaded', () => {
            const wrapper = document.getElementById('bcard-' + _newBid);
            if (wrapper) {
                wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
                wrapper.querySelector('.bcard').classList.add('bcard-new');
            }
        });
    }

    function escHtmlCat(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function appendCatToModal(cat) {
        const list = document.querySelector('.cat-list');
        if (!list) return;

        const csrf    = document.querySelector('meta[name="csrf-token"]').content;
        const safe    = cat.name.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
        const escaped = cat.name.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        const typeCls = cat.type.toLowerCase();

        const li = document.createElement('li');
        li.className = 'cat-item';
        li.id        = 'cat-row-' + cat.id;
        const acctHint = (cat.type === 'Income' && cat.account_name)
            ? `<span class="cat-account-hint"><i class="ti ti-arrow-right"></i> ${escHtmlCat(cat.account_name)}</span>` : '';

        li.innerHTML = `
            <div class="cat-view d-flex align-items-center gap-2">
                <span class="cat-type-badge cat-type-${typeCls}">${cat.type}</span>
                <div class="flex-grow-1 min-w-0">
                    <span class="cat-name">${escaped}</span>${acctHint}
                </div>
                <button type="button" class="bcard-btn bcard-btn-edit"
                        onclick="openCatEdit(${cat.id}, '${safe}', '${cat.type}', '${cat.account_id ?? ''}')" title="Edit">
                    <i class="ti ti-pencil"></i>
                </button>
                <form action="/categories/${cat.id}" method="POST" class="d-inline"
                      onsubmit="return confirm('Delete category \\u2018${safe}\\u2019? This cannot be undone.')">
                    <input type="hidden" name="_token" value="${csrf}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="bcard-btn bcard-btn-delete" title="Delete">
                        <i class="ti ti-trash"></i>
                    </button>
                </form>
            </div>
            <form class="cat-edit-form d-none d-flex gap-2 align-items-end flex-wrap"
                  action="/categories/${cat.id}" method="POST">
                <input type="hidden" name="_token" value="${csrf}">
                <input type="hidden" name="_method" value="PUT">
                <div class="flex-grow-1" style="min-width:100px;">
                    <input type="text" name="name" class="form-control form-control-sm"
                           value="${escaped}" maxlength="100" required>
                </div>
                <div style="width:100px;">
                    <select name="type" class="form-select form-select-sm cat-edit-type-sel">
                        <option value="Expense" ${cat.type === 'Expense' ? 'selected' : ''}>Expense</option>
                        <option value="Income"  ${cat.type === 'Income'  ? 'selected' : ''}>Income</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm px-2 flex-shrink-0"
                        style="height:32px;" title="Save">
                    <i class="ti ti-device-floppy"></i>
                </button>
                <button type="button" class="btn btn-light btn-sm px-2 flex-shrink-0"
                        style="height:32px;" onclick="closeCatEdit(${cat.id})" title="Cancel">
                    <i class="ti ti-x"></i>
                </button>
            </form>`;

        list.appendChild(li);

        // Scroll the new item into view
        li.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        // Update counter badge
        const badge = document.getElementById('catCountBadge');
        if (badge) {
            const total = document.querySelectorAll('.cat-item').length;
            badge.textContent = total + ' ' + (total === 1 ? 'category' : 'categories');
        }
    }

    function addCatToDropdown(cat) {
        const select = document.getElementById('add-category_id');
        if (!select) return;

        const opt       = document.createElement('option');
        opt.value       = cat.id;
        opt.dataset.name = cat.name;
        opt.textContent  = cat.name;
        select.appendChild(opt);
    }

    // ── Inline category edit helpers ─────────────────────────────────────
    function openCatEdit(id, name, type, accountId) {
        const item     = document.getElementById('cat-row-' + id);
        const editForm = item.querySelector('.cat-edit-form');
        const acctWrap = editForm.querySelector('.cat-edit-account-wrap');
        const typeSel  = editForm.querySelector('.cat-edit-type-sel');

        // Show/hide account picker based on type
        if (acctWrap) acctWrap.classList.toggle('d-none', type !== 'Income');

        // Wire up type change to toggle account picker
        typeSel?.addEventListener('change', function () {
            acctWrap?.classList.toggle('d-none', this.value !== 'Income');
        });

        item.querySelector('.cat-view').classList.add('d-none');
        editForm.classList.remove('d-none');
    }

    function closeCatEdit(id) {
        const item = document.getElementById('cat-row-' + id);
        item.querySelector('.cat-view').classList.remove('d-none');
        item.querySelector('.cat-edit-form').classList.add('d-none');
    }
</script>
@endpush
