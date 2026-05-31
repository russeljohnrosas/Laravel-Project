@extends('layouts.app')

@section('title', 'My Expenses')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-semibold" style="color:#1F2937;">My Expenses</h4>
        <p class="text-muted mb-0 small">{{ $expenses->count() }} record{{ $expenses->count() !== 1 ? 's' : '' }}</p>
    </div>
    <a href="{{ route('expenses.create') }}" class="btn btn-success">
        <i class="ti ti-plus me-1"></i>Add Expense
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                    <tr>
                        <td class="ps-4 text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <span class="badge rounded-pill"
                                  style="background:#EEF3EB; color:#5a7052; font-weight:600; font-size:.75rem;">
                                {{ $expense->category }}
                            </span>
                        </td>
                        <td class="fw-semibold">₱{{ number_format($expense->amount, 2) }}</td>
                        <td class="text-muted">{{ \Carbon\Carbon::parse($expense->date)->format('M d, Y') }}</td>
                        <td class="text-muted" style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $expense->description ?: '—' }}
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('expenses.edit', $expense->id) }}"
                               class="btn btn-sm btn-primary me-1"
                               title="Edit">
                                <i class="ti ti-pencil"></i> Edit
                            </a>
                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST"
                                  style="display:inline;"
                                  onsubmit="return confirm('Are you sure you want to delete this expense?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                    <i class="ti ti-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="ti ti-receipt-off" style="font-size:2rem; display:block; margin-bottom:.75rem; opacity:.3;"></i>
                            No expenses yet. <a href="{{ route('expenses.create') }}">Add your first one!</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
