<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (! $user) {
            $this->command->warn('No users found. Run UserSeeder or DatabaseSeeder first.');
            return;
        }

        $find = fn (string $name) => Category::where('user_id', $user->id)
            ->where('name', $name)
            ->value('id');

        $budgets = [
            ['category' => 'Food & Dining',  'amount' => 3000.00],
            ['category' => 'Transportation', 'amount' => 1500.00],
            ['category' => 'Entertainment',  'amount' => 1000.00],
            ['category' => 'Shopping',       'amount' => 2000.00],
        ];

        foreach ($budgets as $data) {
            $categoryId = $find($data['category']);

            if (! $categoryId) {
                $this->command->warn('Category not found: ' . $data['category'] . ' — skipping.');
                continue;
            }

            Budget::firstOrCreate(
                [
                    'user_id'     => $user->id,
                    'category_id' => $categoryId,
                    'month'       => '2026-05-01',
                ],
                [
                    'amount' => $data['amount'],
                ]
            );
        }

        $this->command->info('Budgets seeded for user: ' . $user->email);
    }
}
