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
            $table->index(['user_id', 'type', 'date']);
            $table->index('target_wallet_id');
        });

        Schema::table('todos', function (Blueprint $table) {
            $table->index(['user_id', 'is_completed']);
            $table->index(['user_id', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_completed']);
            $table->dropIndex(['user_id', 'due_date']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'type', 'date']);
            $table->dropIndex(['target_wallet_id']);
        });
    }
};
