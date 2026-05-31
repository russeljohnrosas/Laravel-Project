<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->warn('No users found. Skipping ExpenseSeeder.');
            return;
        }

        $expenses = [
            ['category' => 'Food',           'amount' => 350.00, 'date' => '2026-05-01', 'description' => 'Grocery shopping'],
            ['category' => 'Transportation', 'amount' => 150.00, 'date' => '2026-05-03', 'description' => 'Jeepney & bus fare'],
            ['category' => 'Entertainment',  'amount' => 500.00, 'date' => '2026-05-10', 'description' => 'Movie tickets'],
            ['category' => 'Food',           'amount' => 200.00, 'date' => '2026-05-15', 'description' => 'Restaurant dinner'],
            ['category' => 'Other',          'amount' => 800.00, 'date' => '2026-05-20', 'description' => 'School supplies'],
            ['category' => 'Transportation', 'amount' => 120.00, 'date' => '2026-05-25', 'description' => 'Grab ride'],
            ['category' => 'Food',           'amount' => 450.00, 'date' => '2026-06-01', 'description' => 'Weekly groceries'],
        ];

        foreach ($expenses as $data) {
            Expense::create(array_merge($data, ['user_id' => $user->id]));
        }

        $this->command->info('Seeded ' . count($expenses) . ' expenses for user: ' . $user->email);
    }
}
