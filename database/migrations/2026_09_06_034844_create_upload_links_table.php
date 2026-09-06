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
        Schema::create('upload_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('token', 64)->unique();
            $table->text('description')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->unsignedInteger('max_files')->default(5);
            $table->unsignedInteger('uploaded_files_count')->default(0);
            $table->unsignedInteger('max_file_size_mb')->default(25);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_links');
    }
};
