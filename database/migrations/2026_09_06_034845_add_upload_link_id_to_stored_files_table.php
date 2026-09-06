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
        Schema::table('stored_files', function (Blueprint $table) {
            $table->foreignId('upload_link_id')->nullable()->after('user_id')->constrained('upload_links')->nullOnDelete();
            $table->string('uploader_name', 100)->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stored_files', function (Blueprint $table) {
            $table->dropForeign(['upload_link_id']);
            $table->dropColumn(['upload_link_id', 'uploader_name']);
        });
    }
};
