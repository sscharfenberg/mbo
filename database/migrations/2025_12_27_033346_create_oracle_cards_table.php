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
        Schema::create('oracle_cards', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->uuid('id')->primary();
            $table->uuid('oracle_id')->nullable();
            $table->string('name', 64);
            $table->string('collector_number', 8);
            $table->enum('layout', config('mbo.scryfall.card_layouts'));
            $table->string('type_line', 64);
            $table->enum('lang', config('mbo.scryfall.lang'));
            $table->float('cmc');
            $table->string('mana_cost', 64);
            $table->set('color_identity', config('mbo.scryfall.color_identity'));
            $table->set('colors', config('mbo.scryfall.color_identity'))->nullable();
            $table->string('oracle_text', 256);
            $table->json('legalities');
            $table->json('image_uris')->nullable();
            $table->boolean('reserved')->default(false);
            $table->boolean('game_changer')->default(false);
            $table->string('scryfall_uri', 255);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oracle_cards');
    }
};
