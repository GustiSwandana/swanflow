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
            $table->decimal('admin_fee', 15, 2)->default(0)->after('amount');
            $table->string('fee_payer', 20)->default('source')->after('admin_fee'); // 'source' or 'destination'
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['admin_fee', 'fee_payer']);
        });
    }
};
