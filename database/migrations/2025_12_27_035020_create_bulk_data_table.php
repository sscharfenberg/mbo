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
        Schema::create('bulk_data', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->uuid('id')->primary();
            $table->string('type', 20)->unique();
            $table->timestamp('updated_at');
            $table->string('uri', 128);
            $table->string('name', 32);
            $table->string('description', 255);
            $table->unsignedInteger('size');
            $table->string('download_uri', 128);
            $table->string('content_type', 32);
            $table->string('content_encoding', 16);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_data');
    }
};
