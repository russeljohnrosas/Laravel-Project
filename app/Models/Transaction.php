<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Account;

class Transaction extends Model
{
    // Allow mass assignment for these fields
    protected $fillable = [
        'user_id',
        'category_id',
        'account_id',
        'description',
        'type',
        'amount',
        'date',
        'notes',
    ];

    // Cast these fields to proper types
    protected $casts = [
        'amount' => 'decimal:2',
        'date'   => 'date',
    ];

    // Transaction belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Transaction belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Transaction belongs to an account (expense deduction account)
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
