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
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wallet_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // debt (utang saya), receivable (piutang saya)
            $table->string('person_name');
            $table->decimal('amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0.00);
            $table->date('due_date')->nullable();
            $table->string('status')->default('unpaid'); // unpaid, partially_paid, paid
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
