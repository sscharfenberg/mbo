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
            $table->string('name', 160);
            $table->string('collector_number', 10);
            $table->string('layout', 48);
            $table->string('lang', 8);
            $table->string('card_image_0')->nullable();
            $table->string('card_image_1')->nullable();
            $table->string('art_crop')->nullable();
            $table->unsignedTinyInteger('finishes')->default(1); // = "nonfoil"
            $table->unsignedTinyInteger('games');
            $table->json('prices');
            $table->boolean('digital')->default(false);
            $table->string('rarity', 32);
            $table->foreignUuid('artist_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
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
