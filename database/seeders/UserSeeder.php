<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'            => 'Test User',
                'password'        => Hash::make('password'),
                'profile_picture' => null,
                'is_admin'        => true,
            ]
        );

        // Ensure existing seed user gets admin flag
        if (! $user->wasRecentlyCreated && ! $user->is_admin) {
            $user->update(['is_admin' => true]);
        }

        $this->command->info('Admin user ready — email: test@example.com / password: password');
    }
}
