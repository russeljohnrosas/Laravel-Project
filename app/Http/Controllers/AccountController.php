<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    // Show all accounts for the logged-in user
    public function index()
    {
        $userId   = session('user')['id'];
        $accounts = Account::where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $grouped = $accounts->groupBy('type');

        // Calculate net worth from assets and liabilities
        $assets      = $accounts->filter->isAsset()->sum('balance');
        $liabilities = $accounts->filter->isLiability()->sum('balance');
        $netWorth    = $assets - $liabilities;

        return view('accounts.index', compact('accounts', 'grouped', 'assets', 'liabilities', 'netWorth'));
    }

    // Add a new account
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'name'      => 'required|string|max:100',
            'type'      => 'required|in:Debit,Credit,Lent,Savings',
            'balance'   => 'required|numeric|min:0',
            'available' => 'nullable|numeric|min:0',
            'due_date'  => 'nullable|date',
            'notes'     => 'nullable|string|max:255',
        ]);

        // Create the account
        Account::create([
            'user_id'   => session('user')['id'],
            'name'      => $request->name,
            'type'      => $request->type,
            'balance'   => $request->balance,
            'available' => $request->available,
            'due_date'  => $request->due_date,
            'notes'     => $request->notes,
            'is_active' => true,
        ]);

        return back()->with('success', 'Account "' . $request->name . '" added.');
    }

    // Update an account
    public function update(Request $request, $id)
    {
        // Find account and make sure it belongs to user
        $account = Account::where('id', $id)->where('user_id', session('user')['id'])->first();

        // Validate input
        $request->validate([
            'name'      => 'required|string|max:100',
            'type'      => 'required|in:Debit,Credit,Lent,Savings',
            'balance'   => 'required|numeric|min:0',
            'available' => 'nullable|numeric|min:0',
            'due_date'  => 'nullable|date',
            'notes'     => 'nullable|string|max:255',
        ]);

        // Update the account
        $account->update([
            'name'      => $request->name,
            'type'      => $request->type,
            'balance'   => $request->balance,
            'available' => $request->available,
            'due_date'  => $request->due_date,
            'notes'     => $request->notes,
        ]);

        return back()->with('success', 'Account updated.');
    }

    // Delete an account
    public function destroy($id)
    {
        // Find account and make sure it belongs to user
        $account = Account::where('id', $id)->where('user_id', session('user')['id'])->first();

        $account->delete();

        return back()->with('success', 'Account deleted.');
    }
}
