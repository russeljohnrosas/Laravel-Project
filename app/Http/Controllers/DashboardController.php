<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\User;

class DashboardController extends Controller
{
    // Show the dashboard
    public function index()
    {
        $userCount    = User::count();
        $expenseCount = Expense::count();

        return view('dashboard.index', compact('userCount', 'expenseCount'));
    }
}
