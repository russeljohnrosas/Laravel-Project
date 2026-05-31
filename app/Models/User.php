<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Allow mass assignment for these fields
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'address',
        'phone',
        'date_of_birth',
        'is_admin',
    ];

    // Hide these fields from arrays and JSON
    protected $hidden = ['password', 'remember_token'];

    // Cast these fields to proper types
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth'     => 'date',
        'is_admin'          => 'boolean',
    ];

    // Check if this user is an admin
    public function isAdmin()
    {
        return $this->is_admin === true;
    }

    // Check if this user is a regular user
    public function isUser()
    {
        return $this->is_admin === false;
    }

    // User has many expenses
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // User has many transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // User has many categories
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    // User has many budgets
    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }
}
