<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    // Allow mass assignment for these fields
    protected $fillable = [
        'user_id', 'name', 'type', 'balance', 'available', 'due_date', 'notes', 'is_active',
    ];

    // Cast these fields to proper types
    protected $casts = [
        'balance'   => 'decimal:2',
        'available' => 'decimal:2',
        'due_date'  => 'date',
        'is_active' => 'boolean',
    ];

    // Account belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Check if this is an asset account (adds to net worth)
    public function isAsset()
    {
        return in_array($this->type, ['Debit', 'Savings']);
    }

    // Check if this is a liability account (subtracts from net worth)
    public function isLiability()
    {
        return in_array($this->type, ['Credit', 'Lent']);
    }
}
