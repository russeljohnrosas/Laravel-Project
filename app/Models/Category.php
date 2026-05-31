<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Allow mass assignment for these fields
    protected $fillable = [
        'user_id',
        'name',
        'icon',
        'color',
        'type',
        'is_active',
        'account_id',
    ];

    // Cast is_active as boolean
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Category belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Category has many transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Category has many budgets
    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    // Category belongs to an account
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
