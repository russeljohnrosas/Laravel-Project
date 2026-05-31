{{--
    Shared form fields for Add and Edit modals.
    $formId — 'add' or 'edit', used to namespace input IDs and repopulate old() values.
--}}
@php $isEdit = ($formId === 'edit'); @endphp

{{-- Hidden field so the controller knows which modal to re-open on error --}}
@if (!$isEdit)
    <input type="hidden" name="_modal" value="add">
@endif

<div class="row g-3">

    {{-- Description --}}
    <div class="col-12">
        <label for="{{ $formId }}-description" class="form-label">Description <span class="text-danger">*</span></label>
        <input type="text"
               id="{{ $formId }}-description"
               name="description"
               class="form-control @error('description') is-invalid @enderror"
               placeholder="e.g. Grocery shopping"
               value="{{ $isEdit ? '' : old('description') }}"
               maxlength="100"
               required>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Category --}}
    <div class="col-12">
        <label for="{{ $formId }}-category_id" class="form-label">Category <span class="text-danger">*</span></label>
        <select id="{{ $formId }}-category_id"
                name="category_id"
                class="form-select @error('category_id') is-invalid @enderror"
                required>
            <option value="">Select a category</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}"
                    {{ (!$isEdit && old('category_id') == $cat->id) ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Type --}}
    <div class="col-12">
        <label class="form-label">Type <span class="text-danger">*</span></label>
        <div class="type-radio-group">
            <label class="type-radio-label type-radio-income">
                <input type="radio" name="type" value="Income"
                       class="{{ $formId }}-type-radio"
                       {{ (!$isEdit && old('type', 'Income') === 'Income') ? 'checked' : '' }}>
                <i class="ti ti-trending-up me-1"></i> Income
            </label>
            <label class="type-radio-label type-radio-expense">
                <input type="radio" name="type" value="Expense"
                       class="{{ $formId }}-type-radio"
                       {{ (!$isEdit && old('type') === 'Expense') ? 'checked' : '' }}>
                <i class="ti ti-trending-down me-1"></i> Expense
            </label>
        </div>
        @error('type')
            <div class="text-danger" style="font-size:.8rem;margin-top:.25rem;">{{ $message }}</div>
        @enderror
    </div>

    {{-- Account picker (shown only for Expense) --}}
    <div class="col-12" id="{{ $formId }}-account-wrap" style="display:none;">
        <label for="{{ $formId }}-account_id" class="form-label">
            Deduct from Account
            <span class="text-muted fw-normal" style="font-size:.75rem;">(optional)</span>
        </label>
        <select id="{{ $formId }}-account_id"
                name="account_id"
                class="form-select">
            <option value="">— No account —</option>
            @foreach ($accounts as $acct)
                <option value="{{ $acct->id }}"
                    {{ (!$isEdit && old('account_id') == $acct->id) ? 'selected' : '' }}>
                    {{ $acct->name }} — ₱{{ number_format($acct->balance, 2) }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Amount & Date --}}
    <div class="col-6">
        <label for="{{ $formId }}-amount" class="form-label">Amount <span class="text-danger">*</span></label>
        <div class="input-group input-group-sm" style="height:38px;">
            <span class="input-group-text" style="border:1.5px solid #E5E7EB;border-right:none;border-radius:8px 0 0 8px;background:#F8FAFC;font-size:.875rem;">₱</span>
            <input type="number"
                   id="{{ $formId }}-amount"
                   name="amount"
                   class="form-control @error('amount') is-invalid @enderror"
                   placeholder="0.00"
                   step="0.01"
                   min="0.01"
                   value="{{ $isEdit ? '' : old('amount') }}"
                   style="border-radius:0 8px 8px 0;border:1.5px solid #E5E7EB;border-left:none;"
                   required>
            @error('amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-6">
        <label for="{{ $formId }}-date" class="form-label">Date <span class="text-danger">*</span></label>
        <input type="date"
               id="{{ $formId }}-date"
               name="date"
               class="form-control @error('date') is-invalid @enderror"
               max="{{ date('Y-m-d') }}"
               value="{{ $isEdit ? '' : old('date', date('Y-m-d')) }}"
               required>
        @error('date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Notes --}}
    <div class="col-12">
        <label for="{{ $formId }}-notes" class="form-label">Notes <span class="text-muted fw-normal">(optional)</span></label>
        <textarea id="{{ $formId }}-notes"
                  name="notes"
                  class="form-control @error('notes') is-invalid @enderror"
                  rows="2"
                  placeholder="Any additional details…">{{ $isEdit ? '' : old('notes') }}</textarea>
        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>

<script>
// Show account picker only when Expense is selected
(function () {
    var radios = document.querySelectorAll('.{{ $formId }}-type-radio');
    var wrap   = document.getElementById('{{ $formId }}-account-wrap');

    function toggle() {
        var isExpense = document.querySelector('.{{ $formId }}-type-radio[value="Expense"]').checked;
        wrap.style.display = isExpense ? '' : 'none';
        if (!isExpense) {
            document.getElementById('{{ $formId }}-account_id').value = '';
        }
    }

    radios.forEach(function (r) { r.addEventListener('change', toggle); });
    toggle(); // run on load to match current selection
})();
</script>
