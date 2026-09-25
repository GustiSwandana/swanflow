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
            $table->index(['user_id', 'folder_id', 'created_at'], 'stored_files_user_folder_created_idx');
            $table->index(['user_id', 'upload_link_id', 'created_at'], 'stored_files_user_drop_created_idx');
            $table->index(['user_id', 'category', 'created_at'], 'stored_files_user_cat_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stored_files', function (Blueprint $table) {
            $table->dropIndex('stored_files_user_folder_created_idx');
            $table->dropIndex('stored_files_user_drop_created_idx');
            $table->dropIndex('stored_files_user_cat_created_idx');
        });
    }
};
