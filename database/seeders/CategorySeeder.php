<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->warn('No users found. Run UserSeeder or DatabaseSeeder first.');
            return;
        }

        $categories = [
            // Expense categories
            ['name' => 'Food & Dining',  'type' => 'Expense', 'icon' => 'ti-tools-kitchen-2', 'color' => '#EF4444'],
            ['name' => 'Transportation', 'type' => 'Expense', 'icon' => 'ti-car',              'color' => '#F59E0B'],
            ['name' => 'Entertainment',  'type' => 'Expense', 'icon' => 'ti-movie',            'color' => '#8B5CF6'],
            ['name' => 'Shopping',       'type' => 'Expense', 'icon' => 'ti-shopping-cart',    'color' => '#EC4899'],
            ['name' => 'Utilities',      'type' => 'Expense', 'icon' => 'ti-bulb',             'color' => '#06B6D4'],

            // Income categories
            ['name' => 'Salary',         'type' => 'Income',  'icon' => 'ti-cash',             'color' => '#10B981'],
            ['name' => 'Freelance',      'type' => 'Income',  'icon' => 'ti-device-laptop',    'color' => '#3B82F6'],
        ];

        foreach ($categories as $data) {
            Category::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name'    => $data['name'],
                ],
                [
                    'type'      => $data['type'],
                    'icon'      => $data['icon'],
                    'color'     => $data['color'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Categories seeded for user: ' . $user->email);
    }
}
