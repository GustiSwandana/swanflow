<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['user_id', 'wallet_id', 'date'], 'transactions_user_wallet_date_idx');
            $table->index(['wallet_id', 'date'], 'transactions_wallet_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_user_wallet_date_idx');
            $table->dropIndex('transactions_wallet_date_idx');
        });
    }
};
