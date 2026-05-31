<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    // Allow mass assignment for these fields
    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'month',
    ];

    // Cast these fields to proper types
    protected $casts = [
        'amount' => 'decimal:2',
        'month'  => 'date',
    ];

    // Budget belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Budget belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
