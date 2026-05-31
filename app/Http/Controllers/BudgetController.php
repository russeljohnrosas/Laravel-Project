<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    // Show all budgets for the logged-in user
    public function index(Request $request)
    {
        $userId = session('user')['id'];

        // Get selected month or default to current month
        $month = $request->filled('month')
            ? Carbon::createFromFormat('Y-m', $request->input('month'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        // Get budgets and calculate how much was spent in each category
        $budgets = Budget::with('category')
            ->where('user_id', $userId)
            ->whereYear('month', $month->year)
            ->whereMonth('month', $month->month)
            ->orderBy('created_at')
            ->get()
            ->map(function ($budget) use ($month) {
                $catType = $budget->category?->type ?? 'Expense';

                // Calculate how much was spent for this budget category
                $spent = Transaction::where('user_id', $budget->user_id)
                    ->where('category_id', $budget->category_id)
                    ->where('type', $catType)
                    ->whereYear('date', $month->year)
                    ->whereMonth('date', $month->month)
                    ->sum('amount');

                $spent      = (float) $spent;
                $budgeted   = (float) $budget->amount;
                $remaining  = $budgeted - $spent;
                $percentage = $budgeted > 0 ? min(round($spent / $budgeted * 100, 1), 100) : 0;

                $budget->spent      = $spent;
                $budget->remaining  = $remaining;
                $budget->percentage = $percentage;

                return $budget;
            });

        // Filter to expense budgets only
        $expenseBudgets = $budgets->filter(fn ($b) => ($b->category?->type ?? 'Expense') === 'Expense')->values();

        $expenseTotalBudgeted  = $expenseBudgets->sum('amount');
        $expenseTotalSpent     = $expenseBudgets->sum('spent');
        $expenseTotalRemaining = $expenseTotalBudgeted - $expenseTotalSpent;

        $totalBudgeted  = $expenseTotalBudgeted;
        $totalSpent     = $expenseTotalSpent;
        $totalRemaining = $expenseTotalRemaining;

        // Get income categories and how much was earned this month
        $incomeCategories = Category::with('account')
            ->where('user_id', $userId)
            ->where('type', 'Income')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($cat) use ($userId, $month) {
                $earned = Transaction::where('user_id', $userId)
                    ->where('category_id', $cat->id)
                    ->where('type', 'Income')
                    ->whereYear('date', $month->year)
                    ->whereMonth('date', $month->month)
                    ->sum('amount');

                $txCount = Transaction::where('user_id', $userId)
                    ->where('category_id', $cat->id)
                    ->where('type', 'Income')
                    ->whereYear('date', $month->year)
                    ->whereMonth('date', $month->month)
                    ->count();

                $cat->earned   = (float) $earned;
                $cat->tx_count = $txCount;
                return $cat;
            });

        $incomeTotalEarned = $incomeCategories->sum('earned');

        // Get all budgeted category IDs for this month
        $budgetedCategoryIds = $budgets->pluck('category_id');

        // Get expense categories not yet budgeted (for Add Budget modal)
        $availableExpenseCategories = Category::where('user_id', $userId)
            ->where('is_active', true)
            ->where('type', 'Expense')
            ->whereNotIn('id', $budgetedCategoryIds)
            ->orderBy('name')
            ->get();

        // Get income categories not yet budgeted (for Add Budget modal)
        $availableIncomeCategories = Category::where('user_id', $userId)
            ->where('is_active', true)
            ->where('type', 'Income')
            ->whereNotIn('id', $budgetedCategoryIds)
            ->orderBy('name')
            ->get();

        $availableCategories = $availableExpenseCategories;

        // All active expense categories (for edit dropdown)
        $allCategories = Category::where('user_id', $userId)
            ->where('is_active', true)
            ->where('type', 'Expense')
            ->orderBy('name')
            ->get();

        // All categories for the Manage Categories panel
        $managedCategories = Category::with('account')
            ->where('user_id', $userId)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        // User accounts for the income category account picker
        $userAccounts = Account::where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $budgetByCategory = $expenseBudgets->pluck('amount', 'category_id');

        // Build a list of months for the month filter dropdown
        $monthOptions = collect(range(-1, 11))->mapWithKeys(function ($offset) {
            $m = Carbon::now()->startOfMonth()->subMonths($offset);
            return [$m->format('Y-m') => $m->format('F Y')];
        });

        return view('budgets.index', compact(
            'budgets',
            'expenseBudgets',
            'incomeCategories',
            'month',
            'expenseTotalBudgeted',
            'expenseTotalSpent',
            'expenseTotalRemaining',
            'incomeTotalEarned',
            'totalBudgeted',
            'totalSpent',
            'totalRemaining',
            'availableCategories',
            'availableExpenseCategories',
            'availableIncomeCategories',
            'allCategories',
            'managedCategories',
            'userAccounts',
            'budgetByCategory',
            'monthOptions',
        ));
    }

    // Add a new budget
    public function store(Request $request)
    {
        $userId = session('user')['id'];
        $month  = Carbon::createFromFormat('Y-m', $request->input('month', now()->format('Y-m')))
            ->startOfMonth()
            ->toDateString();

        // Validate input
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:0.01',
        ]);

        // Create the budget
        Budget::create([
            'user_id'     => $userId,
            'category_id' => $request->category_id,
            'amount'      => $request->amount,
            'month'       => $month,
        ]);

        return back()->with('success', 'Budget category added successfully.');
    }

    // Update a budget
    public function update(Request $request, $id)
    {
        $budget = Budget::find($id);

        // Make sure the budget belongs to the user
        if (!$budget || $budget->user_id !== session('user')['id']) {
            return back()->with('error', 'Budget not found.');
        }

        // Validate input
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:0.01',
        ]);

        // Update the budget
        $budget->update([
            'category_id' => $request->category_id,
            'amount'      => $request->amount,
        ]);

        return back()->with('success', 'Budget updated successfully.');
    }

    // Delete a budget
    public function destroy($id)
    {
        $budget = Budget::find($id);

        // Make sure the budget belongs to the user
        if (!$budget || $budget->user_id !== session('user')['id']) {
            return back()->with('error', 'Budget not found.');
        }

        $budget->delete();

        return back()->with('success', 'Budget deleted successfully.');
    }
}
