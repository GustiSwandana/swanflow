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
            $table->foreignId('target_wallet_id')
                ->nullable()
                ->after('wallet_id')
                ->constrained('wallets')
                ->cascadeOnDelete();

            $table->foreignId('category_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['target_wallet_id']);
            $table->dropColumn('target_wallet_id');
            $table->foreignId('category_id')->nullable(false)->change();
        });
    }
};
