@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<h4 class="mb-4 fw-semibold" style="color:#1F2937;">
    Welcome, {{ session('user')['name'] }}!
</h4>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">

    <div class="col-12 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background:#EFF6FF;">
                    <i class="ti ti-users fs-4" style="color:#3B82F6;"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Users</div>
                    <div class="fw-bold fs-3" style="color:#1F2937;">{{ $userCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background:#F0FDF4;">
                    <i class="ti ti-receipt fs-4" style="color:#22C55E;"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Expenses</div>
                    <div class="fw-bold fs-3" style="color:#1F2937;">{{ $expenseCount }}</div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Bar Chart --}}
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h6 class="fw-semibold mb-3" style="color:#374151;">Overview</h6>
        <div style="position:relative; height:280px;">
            <canvas id="myChart"></canvas>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Users', 'Expenses'],
            datasets: [{
                label: 'Count',
                data: [{{ $userCount }}, {{ $expenseCount }}],
                backgroundColor: ['#007bff', '#28a745'],
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
</script>
@endpush
