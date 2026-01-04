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
        Schema::create('printed_cards', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->uuid('id')->primary();
            $table->string('name', 155);
            $table->string('collector_number', 10);
            $table->enum('layout', config('mbo.scryfall.card_layout'));
            $table->enum('lang', config('mbo.scryfall.lang'));
            $table->json('image_uris')->nullable();
            $table->json('finishes');
            $table->json('games');
            $table->json('prices');
            $table->boolean('digital')->default(false);
            $table->enum('rarity', config('mbo.scryfall.rarity'));
            $table->foreignUuid('set_id')
                ->constrained()
                ->references('id')
                ->on('sets')
                ->onDelete('cascade');
            $table->foreignUuid('oracle_id')
                ->nullable()
                ->constrained()
                ->references('id')
                ->on('oracle_cards')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printed_cards');
    }
};
