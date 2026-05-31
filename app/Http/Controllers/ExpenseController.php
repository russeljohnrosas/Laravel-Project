<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    // Get the current logged-in user's ID from session
    private function userId(): int
    {
        return session('user')['id'];
    }

    // Show all expenses for the logged-in user
    public function index()
    {
        $expenses = Expense::where('user_id', $this->userId())
            ->orderBy('date', 'desc')
            ->get();

        return view('expenses.index', compact('expenses'));
    }

    // Show create form
    public function create()
    {
        return view('expenses.create');
    }

    // Save new expense
    public function store(Request $request)
    {
        $request->validate([
            'category'    => 'required|string',
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        Expense::create([
            'user_id'     => $this->userId(),
            'category'    => $request->category,
            'amount'      => $request->amount,
            'date'        => $request->date,
            'description' => $request->description,
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense added successfully!');
    }

    // Show edit form
    public function edit($id)
    {
        $expense = Expense::findOrFail($id);

        // Only the owner can edit
        if ($expense->user_id !== $this->userId()) {
            abort(403);
        }

        return view('expenses.edit', compact('expense'));
    }

    // Update expense
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        // Only the owner can update
        if ($expense->user_id !== $this->userId()) {
            abort(403);
        }

        $request->validate([
            'category'    => 'required|string',
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        $expense->update([
            'category'    => $request->category,
            'amount'      => $request->amount,
            'date'        => $request->date,
            'description' => $request->description,
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully!');
    }

    // Delete expense
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);

        // Only the owner can delete
        if ($expense->user_id !== $this->userId()) {
            abort(403);
        }

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully!');
    }
}
