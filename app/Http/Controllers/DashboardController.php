<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = session('user');
        $userId  = $user['id'];
        $isAdmin = $user['is_admin'] ?? false;

        if ($isAdmin) {
            return view('dashboard', [
                'isAdmin'           => true,
                'totalUsers'        => User::count(),
                'totalTransactions' => Transaction::count(),
            ]);
        }

        $now = Carbon::now();

        // All-time totals for balance
        $allIncome   = (float) Transaction::where('user_id', $userId)->where('type', 'Income')->sum('amount');
        $allExpenses = (float) Transaction::where('user_id', $userId)->where('type', 'Expense')->sum('amount');
        $totalBalance = $allIncome - $allExpenses;

        // Current month
        $monthlyIncome = (float) Transaction::where('user_id', $userId)
            ->where('type', 'Income')
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->sum('amount');

        $monthlyExpenses = (float) Transaction::where('user_id', $userId)
            ->where('type', 'Expense')
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->sum('amount');

        $savingsRate = $monthlyIncome > 0
            ? round((($monthlyIncome - $monthlyExpenses) / $monthlyIncome) * 100, 1)
            : 0;

        // Category breakdown this month (for doughnut chart)
        $categoryBreakdown = Transaction::with('category')
            ->where('user_id', $userId)
            ->where('type', 'Expense')
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get()
            ->map(fn($t) => [
                'name'  => $t->category?->name ?? 'Uncategorized',
                'total' => (float) $t->total,
            ]);

        // Last 6 months income vs expenses (for bar chart)
        $monthlyTrend = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthlyTrend->push([
                'month'    => $month->format('M Y'),
                'income'   => (float) Transaction::where('user_id', $userId)
                    ->where('type', 'Income')
                    ->whereMonth('date', $month->month)
                    ->whereYear('date', $month->year)
                    ->sum('amount'),
                'expenses' => (float) Transaction::where('user_id', $userId)
                    ->where('type', 'Expense')
                    ->whereMonth('date', $month->month)
                    ->whereYear('date', $month->year)
                    ->sum('amount'),
            ]);
        }

        // Last 5 transactions
        $recentTransactions = Transaction::with('category')
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'isAdmin',
            'totalBalance',
            'monthlyIncome',
            'monthlyExpenses',
            'savingsRate',
            'categoryBreakdown',
            'monthlyTrend',
            'recentTransactions',
        ));
    }
}
