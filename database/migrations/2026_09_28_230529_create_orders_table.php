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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->string('client_name');
            $table->string('client_phone')->nullable();
            $table->string('project_title');
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->decimal('discount', 15, 2)->default(0.00);
            $table->string('discount_label')->nullable();
            $table->boolean('is_free')->default(false);
            $table->text('gdrive_url')->nullable();
            $table->foreignId('stored_file_id')->nullable()->constrained('stored_files')->nullOnDelete();
            $table->foreignId('folder_id')->nullable()->constrained('folders')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('status', 32)->default('unpaid')->index(); // unpaid, waiting_verification, verified, rejected
            $table->string('payment_proof_path')->nullable();
            $table->timestamp('payment_submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('wallet_id')->nullable()->constrained('wallets')->nullOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('order_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wallet_id')->nullable()->constrained('wallets')->nullOnDelete();
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('user_id');
        });

        Schema::create('order_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('studio_name')->default('Studio Creative');
            $table->text('bank_instructions')->nullable();
            $table->string('qris_image_path')->nullable();
            $table->string('admin_pin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_settings');
        Schema::dropIfExists('order_bank_accounts');
        Schema::dropIfExists('orders');
    }
};
