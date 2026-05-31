<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateAdminSeeder extends Seeder
{
    // Create the admin user
    public function run(): void
    {
        // Use firstOrCreate so running this twice won't create duplicates
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
            ]
        );
    }
}
