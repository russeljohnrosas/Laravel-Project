<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // Show all transactions for the logged-in user
    public function index(Request $request)
    {
        $userId = session('user')['id'];
        $view   = $request->input('view', 'list');

        // Build query with optional filters
        $query = Transaction::with('category')->where('user_id', $userId);

        if ($request->filled('type'))     $query->where('type', $request->input('type'));
        if ($request->filled('category')) $query->where('category_id', $request->input('category'));
        if ($request->filled('month'))    $query->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$request->input('month')]);
        if ($request->filled('search'))   $query->where('description', 'like', '%' . $request->input('search') . '%');

        $transactions = $query->orderByDesc('date')->orderByDesc('id')->get();

        // Get categories for the filter and add form
        $categories = Category::where('user_id', $userId)->where('is_active', true)->orderBy('name')->get();

        // Get list of months that have transactions (for the month filter)
        $months = Transaction::where('user_id', $userId)
            ->selectRaw('DATE_FORMAT(date, "%Y-%m") as month_value, MIN(DATE_FORMAT(date, "%M %Y")) as month_label')
            ->groupByRaw('DATE_FORMAT(date, "%Y-%m")')
            ->orderByRaw('MIN(date) DESC')
            ->pluck('month_label', 'month_value');

        // Set up the calendar view month
        $calMonth = $request->filled('cal_month')
            ? Carbon::createFromFormat('Y-m', $request->input('cal_month'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        // Get transactions for the calendar month
        $calTransactions = Transaction::with('category')
            ->where('user_id', $userId)
            ->whereYear('date', $calMonth->year)
            ->whereMonth('date', $calMonth->month)
            ->orderBy('date')->orderBy('id')
            ->get();

        // Build a day map for the calendar grid
        $rawDayMap = [];
        foreach ($calTransactions as $t) {
            $key = $t->date->format('Y-m-d');
            if (!isset($rawDayMap[$key])) {
                $rawDayMap[$key] = ['income' => 0, 'expense' => 0, 'items' => []];
            }
            if ($t->type === 'Income') {
                $rawDayMap[$key]['income'] += (float) $t->amount;
            } else {
                $rawDayMap[$key]['expense'] += (float) $t->amount;
            }
            $rawDayMap[$key]['items'][] = $t;
        }

        // Format the day map for JavaScript
        $dayMap = [];
        foreach ($rawDayMap as $dateKey => $data) {
            $items = [];
            foreach ($data['items'] as $t) {
                $items[] = [
                    'id'     => $t->id,
                    'desc'   => $t->description,
                    'cat'    => $t->category?->name ?? '',
                    'type'   => $t->type,
                    'amount' => (float) $t->amount,
                    'time'   => $t->created_at->format('g:i A'),
                ];
            }
            $dayMap[$dateKey] = [
                'income'  => $data['income'],
                'expense' => $data['expense'],
                'items'   => $items,
            ];
        }

        $calMonthIncome  = $calTransactions->where('type', 'Income')->sum('amount');
        $calMonthExpense = $calTransactions->where('type', 'Expense')->sum('amount');

        // Get user's accounts for the expense account picker
        $accounts = Account::where('user_id', $userId)->where('is_active', true)->orderBy('name')->get();

        return view('transactions.index', compact(
            'transactions', 'categories', 'accounts', 'months', 'view',
            'calMonth', 'dayMap', 'calMonthIncome', 'calMonthExpense'
        ));
    }

    // Export transactions to CSV
    public function export(Request $request)
    {
        $userId = session('user')['id'];

        $query = Transaction::with('category')->where('user_id', $userId);

        if ($request->filled('month')) {
            $query->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$request->input('month')]);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $rows = $query->orderByDesc('date')->orderByDesc('id')->get();

        // Build CSV content
        $csv = "Date,Description,Category,Type,Amount,Notes\n";
        foreach ($rows as $t) {
            $csv .= implode(',', [
                $t->date->format('Y-m-d'),
                '"' . str_replace('"', '""', $t->description) . '"',
                '"' . str_replace('"', '""', $t->category?->name ?? '') . '"',
                $t->type,
                number_format($t->amount, 2, '.', ''),
                '"' . str_replace('"', '""', $t->notes ?? '') . '"',
            ]) . "\n";
        }

        $filename = 'transactions_' . now()->format('Y-m-d') . '.csv';

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // Add a new transaction
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'description' => 'required|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'type'        => 'required|in:Income,Expense',
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date|before_or_equal:today',
            'account_id'  => 'nullable|exists:accounts,id',
            'notes'       => 'nullable|string',
        ]);

        // Create the transaction
        Transaction::create([
            'user_id'     => session('user')['id'],
            'description' => $request->description,
            'category_id' => $request->category_id,
            'account_id'  => $request->type === 'Expense' ? $request->account_id : null,
            'type'        => $request->type,
            'amount'      => $request->amount,
            'date'        => $request->date,
            'notes'       => $request->notes,
        ]);

        // Income: add to the category's linked account
        if ($request->type === 'Income') {
            $category = Category::find($request->category_id);
            if ($category && $category->account_id) {
                $account = Account::find($category->account_id);
                if ($account) {
                    $account->balance = $account->balance + $request->amount;
                    $account->save();
                }
            }
        }

        // Expense: deduct from the account the user selected
        if ($request->type === 'Expense' && $request->account_id) {
            $account = Account::find($request->account_id);
            if ($account) {
                $account->balance = $account->balance - $request->amount;
                $account->save();
            }
        }

        return back()->with('success', 'Transaction added successfully.');
    }

    // Update an existing transaction
    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);

        // Check if transaction exists and belongs to user
        if (!$transaction || $transaction->user_id !== session('user')['id']) {
            return back()->with('error', 'Transaction not found.');
        }

        // Validate input
        $request->validate([
            'description' => 'required|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'type'        => 'required|in:Income,Expense',
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date|before_or_equal:today',
            'account_id'  => 'nullable|exists:accounts,id',
            'notes'       => 'nullable|string',
        ]);

        // Reverse the OLD transaction from its account
        if ($transaction->type === 'Income') {
            $oldCategory = Category::find($transaction->category_id);
            if ($oldCategory && $oldCategory->account_id) {
                $account = Account::find($oldCategory->account_id);
                if ($account) {
                    $account->balance = $account->balance - $transaction->amount;
                    $account->save();
                }
            }
        }

        if ($transaction->type === 'Expense' && $transaction->account_id) {
            $account = Account::find($transaction->account_id);
            if ($account) {
                $account->balance = $account->balance + $transaction->amount;
                $account->save();
            }
        }

        // Save updated transaction
        $transaction->update([
            'description' => $request->description,
            'category_id' => $request->category_id,
            'account_id'  => $request->type === 'Expense' ? $request->account_id : null,
            'type'        => $request->type,
            'amount'      => $request->amount,
            'date'        => $request->date,
            'notes'       => $request->notes,
        ]);

        // Apply the NEW transaction to its account
        if ($request->type === 'Income') {
            $newCategory = Category::find($request->category_id);
            if ($newCategory && $newCategory->account_id) {
                $account = Account::find($newCategory->account_id);
                if ($account) {
                    $account->balance = $account->balance + $request->amount;
                    $account->save();
                }
            }
        }

        if ($request->type === 'Expense' && $request->account_id) {
            $account = Account::find($request->account_id);
            if ($account) {
                $account->balance = $account->balance - $request->amount;
                $account->save();
            }
        }

        return back()->with('success', 'Transaction updated successfully.');
    }

    // Delete a transaction
    public function destroy($id)
    {
        $transaction = Transaction::find($id);

        // Check if transaction belongs to user
        if (!$transaction || $transaction->user_id !== session('user')['id']) {
            return back()->with('error', 'Transaction not found.');
        }

        // Reverse income from the category's linked account
        if ($transaction->type === 'Income') {
            $category = Category::find($transaction->category_id);
            if ($category && $category->account_id) {
                $account = Account::find($category->account_id);
                if ($account) {
                    $account->balance = $account->balance - $transaction->amount;
                    $account->save();
                }
            }
        }

        // Reverse expense from the account the user picked
        if ($transaction->type === 'Expense' && $transaction->account_id) {
            $account = Account::find($transaction->account_id);
            if ($account) {
                $account->balance = $account->balance + $transaction->amount;
                $account->save();
            }
        }

        $transaction->delete();

        return back()->with('success', 'Transaction deleted successfully.');
    }
}
