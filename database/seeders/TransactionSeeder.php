<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
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

        $transactions = [
            [
                'description' => 'Jollibee',
                'category_id' => $find('Food & Dining'),
                'type'        => 'Expense',
                'amount'      => 500.00,
                'date'        => '2026-05-14',
                'notes'       => null,
            ],
            [
                'description' => 'Monthly Salary',
                'category_id' => $find('Salary'),
                'type'        => 'Income',
                'amount'      => 25000.00,
                'date'        => '2026-05-13',
                'notes'       => 'May 2026 payroll',
            ],
            [
                'description' => 'Movie Tickets',
                'category_id' => $find('Entertainment'),
                'type'        => 'Expense',
                'amount'      => 300.00,
                'date'        => '2026-05-12',
                'notes'       => null,
            ],
        ];

        foreach ($transactions as $data) {
            if (! $data['category_id']) {
                $this->command->warn('Category not found for: ' . $data['description'] . ' — skipping.');
                continue;
            }

            Transaction::firstOrCreate(
                [
                    'user_id'     => $user->id,
                    'description' => $data['description'],
                    'date'        => $data['date'],
                ],
                [
                    'category_id' => $data['category_id'],
                    'type'        => $data['type'],
                    'amount'      => $data['amount'],
                    'notes'       => $data['notes'],
                ]
            );
        }

        $this->command->info('Transactions seeded for user: ' . $user->email);
    }
}
