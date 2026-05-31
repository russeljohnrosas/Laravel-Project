<div class="mb-3">
    <label class="form-label">Account Name <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" placeholder="e.g. GCash, BPI Savings" maxlength="100" required>
</div>

<div class="mb-3">
    <label class="form-label">Type <span class="text-danger">*</span></label>
    <select name="type" class="form-select" onchange="toggleExtraFields(this)" required>
        <option value="Debit">Debit</option>
        <option value="Savings">Savings</option>
        <option value="Credit">Credit</option>
        <option value="Lent">Lent</option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Balance (₱) <span class="text-danger">*</span></label>
    <div class="input-group">
        <span class="input-group-text modal-input-addon">₱</span>
        <input type="number" name="balance" class="form-control" placeholder="0.00" step="0.01" min="0" required>
    </div>
</div>

<div class="acct-extra" style="display:none;">
    <div class="avl-wrap mb-3">
        <label class="form-label">Available Credit (₱)</label>
        <div class="input-group">
            <span class="input-group-text modal-input-addon">₱</span>
            <input type="number" name="available" class="form-control" placeholder="0.00" step="0.01" min="0">
        </div>
    </div>
    <div class="due-wrap mb-3">
        <label class="form-label">Due Date</label>
        <input type="date" name="due_date" class="form-control">
    </div>
</div>

<div>
    <label class="form-label">Notes</label>
    <input type="text" name="notes" class="form-control" placeholder="Optional notes" maxlength="255">
</div>
