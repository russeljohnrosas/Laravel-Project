@extends('layouts.app')

@section('title', 'Edit Expense')

@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('expenses.index') }}" class="btn btn-light btn-sm">
        <i class="ti ti-arrow-left me-1"></i>Back
    </a>
    <h4 class="mb-0 fw-semibold" style="color:#1F2937;">Edit Expense</h4>
</div>

<div class="card border-0 shadow-sm" style="max-width:520px;">
    <div class="card-body p-4">
        <form action="{{ route('expenses.update', $expense->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Category --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                    <option value="" disabled>Select a category</option>
                    @foreach(['Food', 'Transportation', 'Entertainment', 'Other'] as $cat)
                        <option value="{{ $cat }}" {{ old('category', $expense->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Amount --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">₱</span>
                    <input type="number"
                           name="amount"
                           class="form-control @error('amount') is-invalid @enderror"
                           value="{{ old('amount', $expense->amount) }}"
                           placeholder="0.00"
                           step="0.01"
                           min="0.01"
                           required>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Date --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                <input type="date"
                       name="date"
                       class="form-control @error('date') is-invalid @enderror"
                       value="{{ old('date', \Carbon\Carbon::parse($expense->date)->format('Y-m-d')) }}"
                       required>
                @error('date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">Description <span class="text-muted fw-normal">(optional)</span></label>
                <textarea name="description"
                          class="form-control @error('description') is-invalid @enderror"
                          rows="3"
                          placeholder="What was this expense for?"
                          maxlength="500">{{ old('description', $expense->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy me-1"></i>Update Expense
                </button>
                <a href="{{ route('expenses.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
