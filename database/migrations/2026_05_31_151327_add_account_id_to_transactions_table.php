<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Add account_id column to transactions
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Nullable so existing transactions and income don't need an account
            $table->foreignId('account_id')->nullable()->after('category_id')
                  ->constrained()->nullOnDelete();
        });
    }

    // Remove account_id column
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
        });
    }
};
