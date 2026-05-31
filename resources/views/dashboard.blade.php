@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@if($isAdmin)

    {{-- ADMIN DASHBOARD --}}
    <h2 class="mb-4 fw-bold" style="font-size:1.25rem;color:#1F2937;">System Overview</h2>

    {{-- Admin stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6">
            <div class="stat-card">
                <div class="stat-label mb-2">Total Users</div>
                <div class="stat-value" style="color:#7B9669;">{{ $totalUsers }}</div>
                <div class="stat-note">Registered accounts</div>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="stat-card">
                <div class="stat-label mb-2">Total Transactions</div>
                <div class="stat-value" style="color:#6C8480;">{{ $totalTransactions }}</div>
                <div class="stat-note">All records in system</div>
            </div>
        </div>
    </div>

    {{-- Admin bar chart --}}
    <div class="app-card p-4">
        <div class="app-card-title mb-3">System Overview Chart</div>
        <div style="position:relative;height:280px;">
            <canvas id="adminChart"></canvas>
        </div>
    </div>

@else

    {{-- USER DASHBOARD --}}

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label mb-2">My Transactions</div>
                <div class="stat-value" style="color:#7B9669;">{{ $totalTransactions }}</div>
                <div class="stat-note">Records entered</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label mb-2">Total Balance</div>
                <div class="stat-value" style="color:{{ $totalBalance >= 0 ? '#0F172A' : '#EF4444' }}">
                    ₱{{ number_format($totalBalance, 2) }}
                </div>
                <div class="stat-note">All time</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label mb-2">Monthly Income</div>
                <div class="stat-value" style="color:#10B981;">
                    ₱{{ number_format($monthlyIncome, 2) }}
                </div>
                <div class="stat-note">This month</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label mb-2">Monthly Expenses</div>
                <div class="stat-value" style="color:#EF4444;">
                    ₱{{ number_format($monthlyExpenses, 2) }}
                </div>
                <div class="stat-note">This month</div>
            </div>
        </div>
    </div>

    {{-- Savings rate --}}
    <div class="app-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="stat-label">Savings Rate This Month</span>
            <span class="fw-bold">{{ $savingsRate }}%</span>
        </div>
        <div class="progress" style="height:8px;border-radius:99px;background:#F1F5F9;">
            <div class="progress-bar"
                 style="width:{{ min(max($savingsRate, 0), 100) }}%;
                        background:{{ $savingsRate >= 0 ? '#7B9669' : '#EF4444' }};
                        border-radius:99px;">
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-5">
            <div class="app-card p-4 h-100">
                <div class="app-card-title mb-3">Spending by Category</div>
                @if($categoryBreakdown->isEmpty())
                    <div class="text-center text-muted py-5" style="font-size:.875rem;">
                        No expense data for this month.
                    </div>
                @else
                    <div style="position:relative;height:260px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-12 col-lg-7">
            <div class="app-card p-4 h-100">
                <div class="app-card-title mb-3">
                    Monthly Overview
                    <small class="text-muted fw-normal" style="font-size:.78rem;"> — last 6 months</small>
                </div>
                <div style="position:relative;height:260px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="app-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="app-card-title">Recent Transactions</div>
            <a href="{{ route('transactions.index') }}"
               style="font-size:.8rem;color:#7B9669;font-weight:600;text-decoration:none;">
                View All <i class="ti ti-arrow-right ms-1" style="font-size:.7rem;"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table app-table mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransactions as $tx)
                    <tr>
                        <td style="white-space:nowrap;color:#64748B;font-size:.8rem;">
                            {{ $tx->date->format('d M Y') }}
                        </td>
                        <td>{{ $tx->description }}</td>
                        <td>
                            @if($tx->category)
                                <span style="display:inline-flex;align-items:center;gap:.25rem;padding:.2rem .6rem;border-radius:20px;font-size:.75rem;font-weight:600;background:{{ ($tx->category->color ?? '#6B7280').'20' }};color:{{ $tx->category->color ?? '#475569' }};">
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
                        <td class="text-end {{ $tx->type === 'Income' ? 'amount-income' : 'amount-expense' }}">
                            {{ $tx->type === 'Income' ? '+' : '-' }}₱{{ number_format($tx->amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4" style="font-size:.875rem;">
                            No transactions yet.
                            <a href="{{ route('transactions.index') }}" style="color:#7B9669;">Add your first one.</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endif

@endsection

@push('scripts')
@if($isAdmin)
<script>
    // Admin bar chart - Users vs Transactions
    const ctx = document.getElementById('adminChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Users', 'Transactions'],
            datasets: [{
                label: 'Count',
                data: [{{ $totalUsers }}, {{ $totalTransactions }}],
                backgroundColor: ['rgba(123,150,105,.8)', 'rgba(108,132,128,.8)'],
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#F1F5F9' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@else
<script>
    const categoryData = @json($categoryBreakdown);
    const trendData    = @json($monthlyTrend);

    // Spending by category - doughnut chart
    const catCanvas = document.getElementById('categoryChart');
    if (catCanvas && categoryData.length > 0) {
        new Chart(catCanvas, {
            type: 'doughnut',
            data: {
                labels:   categoryData.map(d => d.name),
                datasets: [{
                    data:            categoryData.map(d => d.total),
                    backgroundColor: ['#7B9669','#6C8480','#BAC8B1','#404E3B','#9BAF8E','#8FA882'],
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ₱${ctx.parsed.toLocaleString('en', { minimumFractionDigits: 2 })}`,
                        }
                    }
                }
            }
        });
    }

    // Monthly trend - bar chart
    const trendCanvas = document.getElementById('trendChart');
    if (trendCanvas) {
        new Chart(trendCanvas, {
            type: 'bar',
            data: {
                labels: trendData.map(d => d.month),
                datasets: [
                    {
                        label:           'Income',
                        data:            trendData.map(d => d.income),
                        backgroundColor: 'rgba(123,150,105,.8)',
                        borderRadius:    6,
                    },
                    {
                        label:           'Expenses',
                        data:            trendData.map(d => d.expenses),
                        backgroundColor: 'rgba(220,38,38,.75)',
                        borderRadius:    6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#F1F5F9' },
                        ticks: { callback: val => '₱' + val.toLocaleString() }
                    }
                },
                plugins: {
                    legend: { position: 'top', align: 'end' },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.dataset.label}: ₱${ctx.parsed.y.toLocaleString('en', { minimumFractionDigits: 2 })}`,
                        }
                    }
                }
            }
        });
    }
</script>
@endif
@endpush
