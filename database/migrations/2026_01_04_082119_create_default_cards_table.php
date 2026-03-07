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
        Schema::create('default_cards', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->uuid('id')->primary();
            $table->string('name', 155);
            $table->string('collector_number', 10);
            $table->string('layout', 32);
            $table->string('lang', 8);
            $table->json('image_uris')->nullable();
            $table->json('finishes');
            $table->json('games');
            $table->json('prices');
            $table->boolean('digital')->default(false);
            $table->string('rarity', 16);
            $table->foreignUuid('set_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignUuid('oracle_id')
                ->nullable()
                ->constrained('oracle_cards')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_cards');
    }
};
