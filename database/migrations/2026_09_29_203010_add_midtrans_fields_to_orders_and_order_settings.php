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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_gateway', 32)->nullable()->after('status');
            $table->string('payment_type', 32)->nullable()->after('payment_gateway');
            $table->string('payment_reference', 128)->nullable()->after('payment_type');
            $table->text('snap_token')->nullable()->after('payment_reference');
            $table->timestamp('paid_at')->nullable()->after('verified_at');
        });

        Schema::table('order_settings', function (Blueprint $table) {
            $table->boolean('midtrans_enabled')->default(false)->after('admin_pin');
            $table->text('midtrans_server_key')->nullable()->after('midtrans_enabled');
            $table->text('midtrans_client_key')->nullable()->after('midtrans_server_key');
            $table->boolean('midtrans_is_production')->default(false)->after('midtrans_client_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_settings', function (Blueprint $table) {
            $table->dropColumn([
                'midtrans_enabled',
                'midtrans_server_key',
                'midtrans_client_key',
                'midtrans_is_production',
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_gateway',
                'payment_type',
                'payment_reference',
                'snap_token',
                'paid_at',
            ]);
        });
    }
};
