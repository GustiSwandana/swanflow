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
            $table->foreignId('folder_id')->nullable()->after('upload_link_id')->constrained('folders')->cascadeOnDelete();
            $table->index(['user_id', 'folder_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stored_files', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
            $table->dropIndex(['user_id', 'folder_id']);
            $table->dropColumn('folder_id');
        });
    }
};
