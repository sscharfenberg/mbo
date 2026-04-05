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
        Schema::create('oracle_card_faces', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->uuid('id')->primary();
            $table->foreignUuid('oracle_card_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('face_index');
            $table->string('name', 160);
            $table->string('mana_cost', 64)->nullable();
            $table->string('type_line', 128);
            $table->string('oracle_text', 2048)->nullable();
            $table->string('colors', 6)->nullable();
            $table->string('power', 8)->nullable();
            $table->string('toughness', 8)->nullable();
            $table->string('loyalty', 8)->nullable();
            $table->string('defense', 8)->nullable();

            $table->unique(['oracle_card_id', 'face_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oracle_card_faces');
    }
};
