<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Add a new category
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'name'          => 'required|string|max:100',
            'type'          => 'required|in:Income,Expense',
            'account_id'    => 'nullable|exists:accounts,id',
            'budget_amount' => 'nullable|numeric|min:0.01',
        ]);

        // Create the category
        $category = Category::create([
            'user_id'    => session('user')['id'],
            'name'       => $request->name,
            'type'       => $request->type,
            'account_id' => $request->account_id,
            'is_active'  => true,
        ]);

        // Also create a budget if it's an Expense category with a budget amount
        $newBudgetId = null;
        if ($request->type === 'Expense' && $request->filled('budget_amount')) {
            $month = Carbon::createFromFormat('Y-m', $request->input('month', now()->format('Y-m')))
                ->startOfMonth()
                ->toDateString();

            $budget = Budget::create([
                'user_id'     => session('user')['id'],
                'category_id' => $category->id,
                'amount'      => $request->budget_amount,
                'month'       => $month,
            ]);
            $newBudgetId = $budget->id;
        }

        // Return JSON if the request expects it (from AJAX)
        if ($request->expectsJson()) {
            $category->load('account');
            return response()->json([
                'success'   => true,
                'category'  => [
                    'id'           => $category->id,
                    'name'         => $category->name,
                    'type'         => $category->type,
                    'account_id'   => $category->account_id,
                    'account_name' => $category->account?->name ?? null,
                ],
                'budget_id' => $newBudgetId,
                'message'   => 'Category "' . $category->name . '" created successfully.',
            ]);
        }

        return back()
            ->with('success', 'Category "' . $request->name . '" created successfully.')
            ->with('open_categories_modal', true)
            ->with('new_budget_id', $newBudgetId);
    }

    // Update a category
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        // Make sure the category belongs to the user
        if (!$category || $category->user_id !== session('user')['id']) {
            return back()->with('error', 'Category not found.');
        }

        // Validate input
        $request->validate([
            'name'       => 'required|string|max:100',
            'type'       => 'required|in:Income,Expense',
            'account_id' => 'nullable|exists:accounts,id',
        ]);

        // Update the category
        $category->update([
            'name'       => $request->name,
            'type'       => $request->type,
            'account_id' => $request->account_id,
        ]);

        return back()
            ->with('success', 'Category updated successfully.')
            ->with('open_categories_modal', true);
    }

    // Delete a category
    public function destroy($id)
    {
        $category = Category::find($id);

        // Make sure the category belongs to the user
        if (!$category || $category->user_id !== session('user')['id']) {
            return back()->with('error', 'Category not found.');
        }

        $category->delete();

        return back()
            ->with('success', 'Category deleted successfully.')
            ->with('open_categories_modal', true);
    }
}
